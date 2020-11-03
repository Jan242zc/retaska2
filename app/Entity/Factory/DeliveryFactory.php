<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\Delivery;


final class DeliveryFactory
{
	public function __construct(){
		
	}

	public function createFromArray(Array $data): Delivery
	{
		if(!$id = $data['id']){
			$id = null;
		}
		return new Delivery($id, $data['name']);
	}
	
	public function createFromObject($object): Delivery
	{
		if(!$id = $object->id){
			$id = null;
		}
		return new Delivery($id, $object->name);
	}
}
