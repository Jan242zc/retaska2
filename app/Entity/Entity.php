<?php

declare(strict_types=1);

namespace App\Entity;

final class Entity
{
	/** @var int */
	private $id;
	
	/** @var string */
	private $name;
	
	/** @var int */
	private $idLimit;
	
	public function __construct(int $id, string $name, int $idLimit){
		$this->id = $id;
		$this->name = $name;
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
