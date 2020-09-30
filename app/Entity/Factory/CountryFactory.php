<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\Country;


final class CountryFactory
{
	public static function createFromArray(Array $data): Country
	{
		if(!$id = $data['id']){
			$id = null;
		}
		return new Country($id, $data['name']);
	}
	
	public static function createFromObject($object): Country
	{
		if(!$id = $object->id){
			$id = null;
		}
		return new Country($id, $object->name);
	}
}
