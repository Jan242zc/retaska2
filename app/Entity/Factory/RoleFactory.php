<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\Role;


final class RoleFactory
{
	public function __construct(){	
	}
	
	public function createFromArray($data): Role
	{
		if(!$data['id']){
			$data['id'] = null;
		}
		
		return new Role($data['id'], $data['name']);
	}
	
	public function createFromObject($object): Role
	{
		if(!$object->id){
			$object->id = null;
		}
		
		return new Role($object->id, $object->name);
	}
}
