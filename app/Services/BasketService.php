<?php

declare(strict_types=1);

namespace App\Services;

use Nette;
use Nette\Http\Session AS Session;
use App\Entity\Basket;
use App\Entity\BasketItem;
use App\Entity\Product;
use App\Services\GeneralServiceInterface\IBasketService;
use App\Services\PriceCalculator;


final class BasketService implements IBasketService
{
	/** var @Basket */
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
	
	public function getAllBasketItems(): Array
	{
		return $this->basketSessionSection->getItems();
	}
	
	//currently used only by this class
	public function getBasketItemsIds(): Array
	{
		return $this->basketSessionSection->getItemsIds();
	}
	
	public function getBasketItemById($id): BasketItem
	{
		return $this->basketSessionSection->getItemById($id);
	}
	
	public function addProductToBasket(Product $product, int $quantity): void
	{
		$price = PriceCalculator::calculateProductTotalPrice($quantity, $product->getPrice());
		$this->basketSessionSection->addItem($product, $quantity, $price);
		$this->updateAllProductsTotalPrice();
	}
	
	//currently used only by this class
	public function removeItemFromBasket($id): void
	{
		if(in_array($id, $this->getBasketItemsIds())){
			$this->basketSessionSection->removeItem($id);
			$this->updateAllProductsTotalPrice();
		}
	}
	
	public function removeAllItemsFromBasket(): void
	{
		$this->basketSessionSection->removeAllItems();
		$this->updateAllProductsTotalPrice();
	}
	
	public function adjustBasketByBasketFormData(Array $idValuesArray, Array $quantitiesArray, Array $toBeDeletedValuesArray): void
	{
		$this->adjustProductsQuantities($idValuesArray, $quantitiesArray);
		$this->removeTheseItemsFromBasket($toBeDeletedValuesArray);
		$this->updateAllProductsTotalPrice();
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
			$productPriceInBasket = PriceCalculator::calculateProductTotalPrice(intval($quantities[$key]), $product->getProduct()->getPrice());
			$product->setPrice($productPriceInBasket);
		}
	}
	
	private function updateAllProductsTotalPrice(): void
	{
		$this->basketSessionSection->setTotalPrice(PriceCalculator::calculateAllProductsTotalPrice($this->basketSessionSection->getItems()));
	}
	
	//currently not used anywhere
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
	
	public function getTotalProductPrice(): float
	{
		return $this->basketSessionSection->getTotalPrice();
	}
}
