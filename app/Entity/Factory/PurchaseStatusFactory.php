<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\PurchaseStatus;


final class PurchaseStatusFactory
{
	public static function createFromArray(array $data): PurchaseStatus
	{
		$meansCancelled = $data['means_cancelled'] ? true : false;
		if(!$data['id']){
			return new PurchaseStatus(null, $data['name'], $meansCancelled);
		} else {
			return new PurchaseStatus($data['id'], $data['name'], $meansCancelled);
		}
	}
	
	public static function createFromObject($object): PurchaseStatus
	{
		$meansCancelled = $object->means_cancelled ? true : false;
		if(!$object->id){
			return new PurchaseStatus(null, $object->name, $meansCancelled);
		} else {
			return new PurchaseStatus($object->id, $object->name, $meansCancelled);
		}
	}
}
