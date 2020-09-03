<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\Category;


final class CategoryFactory
{
	public static function createFromArray(array $data): Category
	{
		if(!$data['id']){
			return new Category(null, $data['name']);
		} else {
			return new Category($data['id'], $data['name']);
		}
	}
	
	public static function createFromObject($object): Category
	{
		if(!$object->id){
			return new Category(null, $object->name);
		} else {
			return new Category($object->id, $object->name);
		}
	}
}
