<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\Purchase;


final class PurchaseFactory
{
	public static function createFromArray(array $data): Purchase
	{
		// if(is_null($data['id'])){
			// $id = null;
		// } else {
			// $id = $data['id'];
		// }
		
		// if(is_null($data['note'])){
			// $note = null;
		// } else {
			// $note = $data['note'];
		// }
		
		$data['id'] ?? null;
		$data['note'] ?? null;
		
		return new Purchase($id, $array['customerName'], $array['customerStreetAndNumber'], $array['customerCity'], $array['customerZip'], $array['customerCountry'], $array['email'], $array['phone'], $array['shipToOtherThanPersonalAdress'], $array['deliveryService'], $array['deliveryStreetAndNumber'], $array['deliveryCity'], $array['deliveryZip'], $array['deliveryCountry'], $note);
	}
	
	public static function createFromObject($object): Purchase
	{
		if(is_null($object->id)){
			$id = null;
		} else {
			$id = $object->id;
		}
		
		if(is_null($object->note)){
			$note = null;
		} else {
			$note = $object->note;
		}
		
		return new Purchase($id, $object->customerName, $object->customerStreetAndNumber, $object->customerCity, $object->customerZip, $object->customerCountry, $object->email, $object->phone, $object->shipToOtherThanPersonalAdress, $object->deliveryService, $object->deliveryStreetAndNumber, $object->deliveryCity, $object->deliveryZip, $object->deliveryCountry, $note);
	}
	
	public static function createFromFinishPurchaseFormData(Array $data, $deliveryService): Purchase
	{
		$data = self::nullEmptyNullableElements($data);
		
		return new Purchase(null, $data['personalData']['name'], $data['personalData']['streetAndNumber'], $data['personalData']['city'], $data['personalData']['zip'], $data['personalData']['country'], $data['personalData']['email'], $data['personalData']['phone'], $data['personalData']['differentAdress'], $deliveryService, $data['deliveryAdress']['streetAndNumber'], $data['deliveryAdress']['city'], $data['deliveryAdress']['zip'], $data['deliveryAdress']['country'], $data['deliveryTerms']['note']);
	}
	
	private static function nullEmptyNullableElements(Array $data): Array
	{
		$data['deliveryTerms']['payment'] = $data['deliveryTerms']['payment'] !== "" ? $data['deliveryTerms']['payment'] : null;
		$data['deliveryAdress']['streetAndNumber'] = $data['deliveryAdress']['streetAndNumber'] !== "" ? $data['deliveryAdress']['streetAndNumber'] : null;
		$data['deliveryAdress']['city'] = $data['deliveryAdress']['city'] !== "" ? $data['deliveryAdress']['city'] : null;
		$data['deliveryAdress']['zip'] = $data['deliveryAdress']['zip'] !== "" ? $data['deliveryAdress']['zip'] : null;
		$data['deliveryAdress']['country'] = $data['deliveryAdress']['country'] !== "" && $data['personalData']['differentAdress'] ? $data['deliveryAdress']['country'] : null;
		$data['deliveryTerms']['note'] = $data['deliveryTerms']['note'] !== "" ? $data['deliveryTerms']['note'] : null;
		
		
		return $data;
	}
}
