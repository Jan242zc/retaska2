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
	
	/** @var string */
	private $password;
	
	public function __construct($id, string $name, Role $role, string $password = null){
		$this->id = $id;
		$this->name = $name;
		$this->role = $role;
		$this->password = $password;
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
	
	public function getPassword()
	{
		return $this->password;
	}
	
	public function setPassword(string $password): void
	{
		$this->password = $password;
	}
	
	public function toArray(): Array
	{
		$array = [
			'id' => $this->id,
			'name' => $this->name,
			'role' => $this->role->getId(),
		];
		
		if(!is_null($this->password)){
			$array['password'] = $this->password;
		}
		
		return $array;
	}
}
