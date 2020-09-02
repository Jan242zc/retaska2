<?php

declare(strict_types=1);

namespace App\Entity;

final class Category
{
	/** @var int */
	private $id;

	/** @var string */
	private $name;
	
	public function __construct(int $id = null, string $name){
		$this->id = $id;
		$this->name = $name;
	}
	
	public function getId(): int
	{
		return $this->id;
	}
	
	public function getName(): string
	{
		return $this->name;
	}
	
	public function setName(string $name): void
	{
		$this->name = $name;
	}
	
	public function toArray(): Array
	{
		return [
			'id' => $this->id,
			'name' => $this->name
		];
	}
}