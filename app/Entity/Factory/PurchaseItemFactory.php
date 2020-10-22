<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\PurchaseItem;


final class PurchaseItemFactory
{
	public static function createFromArray(array $data): PurchaseItem
	{
		if($data['id']){
			$id = $data['id'];
		} else {
			$id = null;
		}
		
		return new PurchaseItem($id, $data['purchase_id'], $data['product_id'], $data['product_name'], $data['product_price'], $data['quantity'], $data['price']);
	}
	
	public static function createFromObject($object): PurchaseItem
	{
		if($object->id){
			$id = $object->id;
		} else {
			$id = null;
		}
		
		return new PurchaseItem($id, $object->purchase_id, $object->product_id, $object->product_name, $object->product_price, $object->quantity, $object->price);
	}
}
