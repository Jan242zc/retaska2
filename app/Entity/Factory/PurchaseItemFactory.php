<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\PurchaseItem;
use App\Entity\BasketItem;


final class PurchaseItemFactory
{
	public function createFromArray(array $data): PurchaseItem
	{
		if($data['id']){
			$id = $data['id'];
		} else {
			$id = null;
		}
		
		if(!isset($data['product_id'])){
			$productId = null;
		} else {
			$productId = $object->product_id;
		}
		
		return new PurchaseItem($id, $data['purchase_id'], $productId, $data['product_name'], $data['product_price'], $data['quantity'], $data['price']);
	}
	
	public function createFromObject($object): PurchaseItem
	{
		if($object->id){
			$id = $object->id;
		} else {
			$id = null;
		}
		
		if(!isset($object->product_id)){
			$productId = null;
		} else {
			$productId = $object->product_id;
		}

		return new PurchaseItem($id, $object->purchase_id, $productId, $object->product_name, $object->product_price, $object->quantity, $object->price);
	}
	
	public function createFromBasketData(Array $basketItems): Array
	{
		$result = [];
		
		foreach($basketItems as $item){
			$result[] = $this->createFromBasketItemData($item);
		}
		
		return $result;
	}
	
	public function createFromBasketItemData(BasketItem $basketItem): PurchaseItem
	{
		return new PurchaseItem(null, null, $basketItem->getProduct()->getId(), $basketItem->getProduct()->getName(), $basketItem->getProduct()->getPrice(), $basketItem->getQuantity(), $basketItem->getPrice());
	}
}
