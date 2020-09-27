<?php

declare(strict_types=1);

namespace App\Services;

use Nette;
use Nette\Http\Session AS Session;
use App\Entity\Basket;
use App\Entity\BasketItem;
use App\Entity\Product;
use App\Services\GeneralServiceInterface\IBasketService;


final class BasketService implements IBasketService
{
	/* what type to hint? */
	private $basketSessionSection;
	
	public function __construct(Session $session){
		$this->basketSessionSection = $this->getThisServicesSessionSection($session);
	}
	
	private function getThisServicesSessionSection($session) //what to return?
	{
		$sessionSection = $session->getSection('basket');
		if(!isset($sessionSection->basket)){
			$sessionSection->basket = new Basket([]);
		}
		return $sessionSection->basket;
	}
	
	public function getBasket() //:??
	{
		return $this->basketSessionSection;
	}
	
	public function getAllBasketItems(): Array
	{
		return $this->basketSessionSection->getItems();
	}
	
	public function getBasketItemsIds(): Array
	{
		return $this->basketSessionSection->getItemsIds();
	}
	
	public function getBasketItemById($id)
	{
		return $this->basketSessionSection->getItemById($id);
	}
	
	public function addProductToBasket(Product $product, int $quantity, float $price)
	{
		$product = $product;
		//$price = $sluzbaNaPocitaniCeny->vypocitejCenu($quantity, $price);
		$price = $quantity * $product->getPrice();
		$this->basketSessionSection->addItem($product, $quantity, $price);
		$oldTotalPrice = $this->basketSessionSection->getTotalPrice() ?? 0;
		$newTotalPrice = $oldTotalPrice += ($quantity * $product->getPrice());
		$this->basketSessionSection->setTotalPrice($newTotalPrice);
	}
	
	public function removeItemFromBasket($id)
	{
		if(in_array($id, $this->getBasketItemsIds())){
			$this->basketSessionSection->removeItem($id);
		}
	}
	
	public function removeAllItemsFromBasket(): void
	{
		$this->basketSessionSection->removeAllItems();
	}
	
	public function adjustBasketByBasketFormData(Array $idValuesArray, Array $quantitiesArray, Array $toBeDeletedValuesArray): void
	{
		$this->adjustProductsQuantities($idValuesArray, $quantitiesArray);
		$this->removeTheseItemsFromBasket($toBeDeletedValuesArray);
	}
	
	private function removeTheseItemsFromBasket(Array $toBeDeletedIds): void
	{
		foreach($toBeDeletedIds as $id){
			$this->removeItemFromBasket(intval($id));
		}
	}
	
	private function adjustProductsQuantities(Array $ids, Array $quantities): void
	{
		foreach($ids as $key => $id){
			$product = $this->getBasketItemById($id);
			$product->setQuantity(intval($quantities[$key]));
			$product->setPrice(intval($quantities[$key]) * $this->getBasketItemById($id)->getProduct()->getPrice());
		}
	}
	
	public function verifyThatThisItemInBasket(int $id): bool
	{
		return in_array($id, $this->getBasketItemsIds());
	}
	
	public function verifyThatAllTheseItemsInBasket(array $ids): bool
	{
		$idsInBasket = $this->getBasketItemsIds();
		foreach($ids as $id){
			if(!in_array($id, $idsInBasket)){
				return false;	
			}
		}
		return true;
	}
}
