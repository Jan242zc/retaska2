<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\DeliveryService;
use App\Entity\Country;
use App\Entity\Payment;
use App\Entity\Delivery;


final class DeliveryServiceFactory
{
	public static function createFromArray(array $data): DeliveryService
	{
		if(!$data['id']){
			return new DeliveryService(null, $data['price'], $data['payment'], $data['delivery'], $data['country']);
		} else {
			return new DeliveryService($data['id'], $data['price'], $data['payment'], $data['delivery'], $data['country']);
		}
	}
	
	public static function createFromObject($object): DeliveryService
	{
		if(!$object->id){
			return new DeliveryService(null, $object->price, $object->payment, $object->delivery, $object->country);
		} else {
			return new DeliveryService($object->id, $object->price, $object->payment, $object->delivery, $object->country);
		}
	}
}
