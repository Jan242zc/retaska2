<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\Country;


final class CountryFactory
{
	public function __construct(){
		
	}
	
	public function createFromArray(Array $data): Country
	{
		if(!$id = $data['id']){
			$id = null;
		}
		return new Country($id, $data['name']);
	}
	
	public function createFromObject($object): Country
	{
		if(!$id = $object->id){
			$id = null;
		}
		return new Country($id, $object->name);
	}
}
