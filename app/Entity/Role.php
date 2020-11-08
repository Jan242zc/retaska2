<?php

declare(strict_types=1);

namespace App\Entity;


final class Role
{
	/** @var int */
	private $id;
	
	/** @var string */
	private $name;
	
	public function __construct($id, string $name){
		$this->id = $id;
		$this->name = $name;
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function setId(): void
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
}
