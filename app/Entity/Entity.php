<?php

declare(strict_types=1);

namespace App\Entity;

final class Entity
{
	/** @var int */
	private $id;
	
	/** @var string */
	private $name;
	
	/** @var string */
	private $nameCzech;
	
	/** @var int */
	private $idLimit;
	
	public function __construct(int $id, string $name, string $nameCzech, int $idLimit){
		$this->id = $id;
		$this->name = $name;
		$this->nameCzech = $nameCzech;
		$this->idLimit = $idLimit;
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
	
	public function getNameCzech(): string
	{
		return $this->nameCzech;
	}
	
	public function setNameCzech(string $nameCzech): void
	{
		$this->nameCzech = $nameCzech;
	}
	
	public function getIdLimit(): int
	{
		return $this->idLimit;
	}
	
	public function setIdLimit(int $idLimit): void
	{
		$this->idLimit = $idLimit;
	}
	
	public function toArray(): Array
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'idLimit' => $this->idLimit
		];
	}
}
