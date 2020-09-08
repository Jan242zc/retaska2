<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Category;


final class Product
{
	/** @var int */
	private $id;
	
	/** @var string */
	private $name;
	
	/** @var float */
	private $price;
	
	/** @var Category */
	private $category;
	
	/** @var string */
	private $material;
	
	/** @var int */
	private $amountAvailable;
	
	/** @var string */
	private $description;
	
	public function __construct($id = null, string $name, float $price, Category $category, string $material, int $amountAvailable, string $description){
		$this->id = $id;
		$this->name = $name;
		$this->price = $price;
		$this->category = $category;
		$this->material = $material;
		$this->amountAvailable = $amountAvailable;
		$this->description = $description;
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function setId(int $id): void
	{
		$this->id = $id;
	}
	
	public function getName(): string
	{
		return $this->name;
	}
	
	public function setName(string $name): void
	{
		$this->name = $name;
	}
	
	public function getPrice(): float
	{
		return $this->price;
	}
	
	public function setPrice(float $price): void
	{
		$this->price = $price;
	}
	
	public function getCategory(): Category
	{
		return $this->category;
	}
	
	public function setCategory(Category $category): void
	{
		$this->category = $category;
	}
	
	public function getMaterial(): string
	{
		return $this->material;
	}
	
	public function setMaterial(string $material): void
	{
		$this->material = $material;
	}
	
	public function getAmountAvailable(): int
	{
		return $this->amountAvailable;
	}
	
	public function setAmountAvailable(int $amountAvailable): void
	{
		$this->amountAvailable = $amountAvailable;
	}
	
	public function getDescription(): string
	{
		return $this->description;
	}
	
	public function setDescription(string $description): void
	{
		$this->description = $description;
	}
	
	public function toArray(): Array
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'price' => $this->price,
			'category' => $this->category->getId(),
			'material' => $this->material,
			'amountAvailable' => $this->amountAvailable,
			'description' => $this->description
		];
	}
}
