<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\CustomerData;


final class CustomerDataFactory
{	
	public static function createFromArray(Array $data, $deliveryService): CustomerData
	{
		$data = self::nullEmptyNullableElements($data);

		return new CustomerData($data['personalData']['name'], $data['personalData']['streetAndNumber'], $data['personalData']['city'], $data['personalData']['zip'], $data['personalData']['country'], $data['personalData']['email'], $data['personalData']['phone'], $data['personalData']['differentAdress'], $deliveryService, $data['deliveryAdress']['streetAndNumber'], $data['deliveryAdress']['city'], $data['deliveryAdress']['zip'], $data['deliveryAdress']['country'], $data['deliveryTerms']['note']);
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
