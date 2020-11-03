<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\PurchaseStatus;


final class PurchaseStatusFactory
{
	public function __construct(){
		
	}
	
	public function createFromArray(array $data): PurchaseStatus
	{
		$defaultForNewPurchases = $data['default_for_new_purchases'] ? true : false;
		$meansCancelled = $data['means_cancelled'] ? true : false;
		if(!$data['id']){
			return new PurchaseStatus(null, $data['name'], $defaultForNewPurchases, $meansCancelled);
		} else {
			return new PurchaseStatus($data['id'], $data['name'], $defaultForNewPurchases, $meansCancelled);
		}
	}
	
	public function createFromObject($object): PurchaseStatus
	{
		$defaultForNewPurchases = $object->default_for_new_purchases ? true : false;
		$meansCancelled = $object->means_cancelled ? true : false;
		if(!$object->id){
			return new PurchaseStatus(null, $object->name, $defaultForNewPurchases, $meansCancelled);
		} else {
			return new PurchaseStatus($object->id, $object->name, $defaultForNewPurchases, $meansCancelled);
		}
	}
}
