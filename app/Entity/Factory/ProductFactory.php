<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity;
use App\Entity\Category;
use App\Entity\Product;


final class ProductFactory
{
	public static function createFromArray(array $data): Product
	{
		if(!$id = $data['id']){
			$id = null;
		}
		return new Product($id, $data['name'], $data['price'], $data['category'], $data['material'], $data['amountAvailable'], $data['description']);
	}
	
	public static function createFromObject($object): Product
	{
		if(!$id = $object->id){
			$id = null;
		}
		return new Product($id, $object->name, $object->price, $object->category, $object->material, $object->amountAvailable, $object->description);
	}
}
