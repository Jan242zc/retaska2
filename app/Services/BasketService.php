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
	
	public function getBasket() //:??
	{
		return $this->basketSessionSection;
	}
	
	public function getAllBasketItems(): Array
	{
		return $this->basketSessionSection->getContents();
	}
	
	public function getBasketItemsIds(): Array
	{
		return $this->basketSessionSection->getContentsIds();
	}
	
	public function getBasketItemById($id)
	{
		return $this->basketSessionSection->getItemById($id);
	}
	
	private function getThisServicesSessionSection($session) //what to return?
	{
		$sessionSection = $session->getSection('basket');
		if(!isset($sessionSection->basket)){
			$sessionSection->basket = new Basket([]);
		}
		return $sessionSection->basket;
	}
	
	public function addProductToBasket(Product $product, int $quantity, float $price)
	{
		$product = $product;
		//$price = $sluzbaNaPocitaniCeny->vypocitejCenu($quantity, $price);
		$price = $quantity * $product->getPrice();
		$this->basketSessionSection->addItem($product, $quantity, $price);
	}
	
	public function removeItemFromBasket($id)
	{
		$this->basketSessionSection->removeItem($id);
	}
	
	public function removeAllItemsFromBasket()
	{
		$this->basketSessionSection->removeAllItems();
	}
}
