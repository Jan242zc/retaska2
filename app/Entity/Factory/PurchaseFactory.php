<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\Purchase;
use App\Entity\CustomerData;
use App\Entity\Basket;


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
		
		if($data['shipToOtherThanCustomerAdress']){
			$shipToOtherThanCustomerAdress = true;
		} else {
			$shipToOtherThanCustomerAdress = false;
		}
		
		return new Purchase($id, $data['customerName'], $data['customerStreetAndNumber'], $data['customerCity'], $data['customerZip'], $data['customerCountry'], $data['email'], $data['phone'], $data['deliveryName'], $data['deliveryPrice'], $data['paymentName'], $data['paymentPrice'], $data['totalPrice'], $shipToOtherThanCustomerAdress, $data['created_at'], $data['purchaseStatus'], $data['purchaseItems'], $data['deliveryStreetAndNumber'], $data['deliveryCity'], $data['deliveryZip'], $data['deliveryCountry'], $data['note']);
	}
	
	public static function createFromObject($object): Purchase
	{
		if($object->id){
			$id = $object->id;
		} else {
			$id = null;
		}
		
		$object = self::nullNullableInObject($object);
		
		if($object->shipToOtherThanCustomerAdress){
			$shipToOtherThanCustomerAdress = true;
		} else {
			$shipToOtherThanCustomerAdress = false;
		}
		
		return new Purchase($id, $object->customerName, $object->customerStreetAndNumber, $object->customerCity, $object->customerZip, $object->customerCountry, $object->email, $object->phone, $object->deliveryName, $object->deliveryPrice, $object->paymentName, $object->paymentPrice, $object->totalPrice, $shipToOtherThanCustomerAdress, $object->created_at, $object->purchaseStatus, $object->purchaseItems, $object->deliveryStreetAndNumber, $object->deliveryCity, $object->deliveryZip, $object->deliveryCountry, $object->note);
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
	
	public static function createFromCustomerData(CustomerData $customerData, float $totalPrice, array $purchaseItems): Purchase
	{
		return new Purchase(
			null,
			$customerData->getName(),
			$customerData->getStreetAndNumber(),
			$customerData->getCity(),
			$customerData->getZip(),
			$customerData->getCountry(),
			$customerData->getEmail(),
			$customerData->getPhone(),
			$customerData->getDeliveryService()->getDelivery()->getName(),
			$customerData->getDeliveryService()->getDeliveryPrice(),
			$customerData->getDeliveryService()->getPayment()->getName(),
			$customerData->getDeliveryService()->getPaymentPrice(),
			$totalPrice,
			$customerData->getDifferentAdress(),
			null,
			null,
			$purchaseItems,
			$customerData->getDeliveryStreetAndNumber(),
			$customerData->getDeliveryCity(),
			$customerData->getDeliveryZip(),
			$customerData->getDeliveryCountry(),
			$customerData->getNote()
		);
	}
}
