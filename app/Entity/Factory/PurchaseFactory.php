<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\Purchase;


final class PurchaseFactory
{
	public static function createFromArray(array $data): Purchase
	{
		if($data['id']){
			$id = $data['id'];
		} else {
			$id = null;
		}
		
		$data = self::nullNullableInArray($data);
		
		return new Purchase($id, $data['customerName'], $data['customerStreetAndNumber'], $data['customerCity'], $data['customerZip'], $data['customerCountry'], $data['email'], $data['phone'], $data['deliveryName'], $data['deliveryPrice'], $data['paymentName'], $data['paymentPrice'], $data['shipToOtherThanCustomerAdress'], $data['purchaseStatus'], $data['purchaseItems'], $data['deliveryStreetAndNumber'], $data['deliveryCity'], $data['deliveryZip'], $data['deliveryCountry'], $data['note']);
	}
	
	public static function createFromObject($object): Purchase
	{
		if($object->id){
			$id = $object->id;
		} else {
			$id = null;
		}
		
		$object = self::nullNullableInObject($object);
		
		return new Purchase($id, $object->customerName, $object->customerStreetAndNumber, $object->customerCity, $object->customerZip, $object->customerCountry, $object->email, $object->phone, $object->deliveryName, $object->deliveryPrice, $object->paymentName, $object->paymentPrice, $object->shipToOtherThanCustomerAdress, $object->purchaseStatus, $object->purchaseItems, $object->deliveryStreetAndNumber, $object->deliveryCity, $object->deliveryZip, $object->deliveryCountry, $object->note);
	}
	
	private static function nullNullableInArray(array $data): array
	{
		$data['deliveryStreetAndNumber'] = $data['deliveryStreetAndNumber'] === '' ? null : $data['deliveryStreetAndNumber'];
		$data['deliveryCity'] = $data['deliveryCity'] === '' ? null : $data['deliveryCity'];
		$data['deliveryZip'] = $data['deliveryZip'] === '' ? null : $data['deliveryZip'];
		$data['deliveryCountry'] = $data['deliveryCountry'] === '' ? null : $data['deliveryCountry'];
		$data['note'] = $data['note'] === '' ? null : $data['note'];
		
		return $data;
	}
	
	private static function nullNullableInObject($object)
	{
		$object->deliveryStreetAndNumber = $object->deliveryStreetAndNumber === '' ? null : $object->deliveryStreetAndNumber;
		$object->deliveryCity = $object->deliveryCity === '' ? null : $object->deliveryCity;
		$object->deliveryZip = $object->deliveryZip === '' ? null : $object->deliveryZip;
		$object->deliveryCountry = $object->deliveryCountry === '' ? null : $object->deliveryCountry;
		$object->note = $object->note === '' ? null : $object->note;
		
		return $object;
	}
}
