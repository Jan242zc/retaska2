<?php

declare(strict_types=1);

namespace App\Entity;


final class PurchaseItem
{
	/** @var int */
	private $id;
	
	/** @var int */
	private $purchaseId;
	
	/** @var int */
	private $productId;
	
	/** @var string */
	private $productName;
	
	/** @var float */
	private $productPrice;
	
	/** @var int */
	private $quantity;
	
	/** @var float */
	private $price;
	
	public function __construct(int $id = null, int $purchaseId = null, int $productId = null, string $productName, float $productPrice, int $quantity, float $price){
		$this->id = $id;
		$this->purchaseId = $purchaseId;
		$this->productId = $productId;
		$this->productName = $productName;
		$this->productPrice = $productPrice;
		$this->quantity = $quantity;
		$this->price = $price;
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function setId(int $id): void
	{
		$this->id = $id;
	}
	
	public function getPurchaseId()
	{
		return $this->purchaseId;
	}
	
	public function setPurchaseId(int $purchaseId): void
	{
		$this->purchaseId = $purchaseId;
	}
	
	public function getProductId()
	{
		return $this->productId;
	}
	
	public function setProductId(int $productId): void
	{
		$this->productId = $productId;
	}
	
	public function getProductName(): string
	{
		return $this->productName;
	}
	
	public function setProductName(string $productName): void
	{
		$this->productName = $productName;
	}
	
	public function getProductPrice(): float
	{
		return $this->productPrice;
	}
	
	public function setProductPrice(float $productPrice): void
	{
		$this->productPrice = $productPrice;
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
	
	public function toArray(): Array
	{
		return [
			'id' => $this->id,
			'purchase_id' => $this->purchaseId,
			'product_id' => $this->productId,
			'product_name' => $this->productName,
			'product_price' => $this->productPrice,
			'quantity' => $this->quantity,
			'price' => $this->price
		];
	}
}
