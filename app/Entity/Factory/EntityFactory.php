<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\Entity;


final class EntityFactory
{
	public function __construct(){
		
	}
	
	public function createFromArray(array $data): Entity
	{
		return new Entity($data['id'], $data['name'], $data['nameCzech'], $data['idLimit']);
	}
	
	public function createFromObject($object): Entity
	{
		return new Entity($object->id, $object->name, $object->nameCzech, $object->idLimit);
	}
}
