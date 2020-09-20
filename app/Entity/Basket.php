<?php

declare(strict_types=1);

namespace App\Entity;

use Nette;
use App\Entity\BasketItem;
use App\Entity\Product;


final class Basket
{
	/** @var Array */
	private $items;
	
	/** @var float */
	private $totalPrice;
	
	public function __construct(Array $items){
		$this->items = $items;
	}
	
	public function getItems(): Array
	{
		return $this->items;
	}
	
	public function getItemById($id): BasketItem
	{
		return $this->items[$id];
	}
	
	public function setItems(Array $items): void
	{
		$this->items = $items;
	}
	
	public function getTotalPrice(): float
	{
		return $this->totalPrice;
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
		return array_keys($this->items());
	}
}
