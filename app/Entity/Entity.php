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
	
	public function set(string $name): void
	{
		$this->name = $name;
	}
	
	public function get(): int
	{
		return $this->idLimit;
	}
	
	public function set(int $idLimit): void
	{
		$this->idLimit = $idLimit;
	}
}
