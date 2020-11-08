<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Role;


final class UserData
{
	/** @var int */
	private $id;
	
	/** @var string */
	private $name;
	
	/** @var Role */
	private $role;
	
	public function __construct($id, string $name, Role $role){
		$this->id = $id;
		$this->name = $name;
		$this->role = $role;
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
	
	public function getRole(): Role
	{
		return $this->role;
	}
	
	public function setRole(Role $role): void
	{
		$this->role = $role;
	}
	
	public function toArray(): Array
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'role' => $this->role->getId()
		];
	}
}
