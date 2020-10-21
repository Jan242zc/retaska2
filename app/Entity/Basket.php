<?php

declare(strict_types=1);

namespace App\Entity;

use Nette;
use App\Entity\BasketItem;
use App\Entity\Product;
use App\Entity\CustomerData;


final class Basket
{
	/** @var Array */
	private $items;
	
	/** @var float */
	private $totalPrice;
	
	/** @var CustomerData */
	private $customerData;

	public function __construct(Array $items){
		$this->items = $items;
		$this->totalPrice = 0;
	}
	
	public function getItems(): Array
	{
		return $this->items;
	}
	
	public function getItemById($id): BasketItem
	{
		return $this->items[intval($id)];
	}
	
	public function setItems(Array $items): void
	{
		$this->items = $items;
	}
	
	public function getTotalPrice(): float
	{
		return $this->totalPrice ?? 0;
	}
	
	public function setTotalPrice(float $totalPrice): void
	{
		$this->totalPrice = $totalPrice;
	}
	
	public function addItem(Product $product, int $quantity, float $price): void
	{
		$this->items[$product->getId()] = new BasketItem($product, $quantity, $price);
	}
	
	public function removeItem($id): void
	{
		if(isset($this->items[$id])){
			unset($this->items[$id]);
		}
	}
	
	public function removeAllItems(): void
	{
		foreach($this->items as $id => $item){
			unset($this->items[$id]);
		}
	}
	
	public function getItemsIds(): Array
	{
		return array_keys($this->items);
	}
	
	public function getCustomerData()
	{
		return $this->customerData;
	}
	
	public function setCustomerData(CustomerData $customerData): void
	{
		$this->customerData = $customerData;
	}
}
