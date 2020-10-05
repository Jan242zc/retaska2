<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\DeliveryCountryPaymentPrices;


final class DeliveryCountryPaymentPricesFactory
{
	public static function createFromArray(Array $data): DeliveryCountryPaymentPrices
	{
		if(!$data['id']){			
			return new DeliveryCountryPaymentPrices(null, $data['delivery'], $data['payment'], $data['deliveryPrice'], $data['paymentPrice'], $data['country']);
		} else {			
			return new DeliveryCountryPaymentPrices($data['id'], $data['delivery'], $data['payment'], $data['deliveryPrice'], $data['paymentPrice'], $data['country']);
		}
	}
	
	public static function createFromObject($object): DeliveryCountryPaymentPrices
	{
		if(!$object->id){			
			return new DeliveryCountryPaymentPrices(null, $object->delivery, $object->payment, $object->deliveryPrice, $object->paymentPrice, $object->country);
		} else {
			return new DeliveryCountryPaymentPrices($object->id, $object->delivery, $object->payment, $object->deliveryPrice, $object->paymentPrice, $object->country);			
		}
	}
}
