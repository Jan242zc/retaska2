<?php

declare(strict_types=1);

namespace App\Entity;

use Nette;
use App\Entity\Product;


final class BasketItem
{
	/** @var Product */
	private $product;
	
	/** @var int */
	private $quantity;
	
	/** @var float */
	private $price;
	
	/** @var bool */
	private $requstedQuantityNotAvailable;
	
	public function __construct(Product $product, int $quantity, float $price){
		$this->product = $product;
		$this->quantity = $quantity;
		$this->price = $price;
		$this->requstedQuantityNotAvailable = false; 
	}
	
	public function getProduct(): Product
	{
		return $this->product;
	}
	
	public function getQuantity(): int
	{
		return $this->quantity;
	}
	
	public function setQuantity(int $quantity): void
	{
		$this->quantity = $quantity;
	}
	
	public function getPrice(): float
	{
		return $this->price;
	}
	
	public function setPrice(float $price): void
	{
		$this->price = $price;
	}
	
	public function getRequstedQuantityNotAvailable(): bool
	{
		return $this->requstedQuantityNotAvailable;
	}
	
	public function setRequstedQuantityNotAvailable(bool $requstedQuantityNotAvailable): void
	{
		$this->requstedQuantityNotAvailable = $requstedQuantityNotAvailable;
	}
}
