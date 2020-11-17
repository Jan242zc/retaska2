<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\CustomerData;
use App\Entity\Country;
use App\Services\Repository\RepositoryInterface\ICountryRepository;


final class CustomerDataFactory
{
	/** @var ICountryRepository */
	private $countryRepository;
	
	public function __construct(ICountryRepository $countryRepository){
		$this->countryRepository = $countryRepository;
	}
	
	public function createFromArray(Array $data, $deliveryService): CustomerData
	{
		$data = $this->nullEmptyNullableElements($data);

		$data['personalData']['country'] = $this->findCountryForCustomerData($data['personalData']['country']);
		if(!is_null($data['deliveryAdress']['country'])){
			$this->findCountryForCustomerData($data['deliveryAdress']['country']);
		}

		return new CustomerData($data['personalData']['name'], $data['personalData']['streetAndNumber'], $data['personalData']['city'], $data['personalData']['zip'], $data['personalData']['country'], $data['personalData']['email'], $data['personalData']['phone'], $data['personalData']['differentAdress'], $deliveryService, $data['deliveryAdress']['streetAndNumber'], $data['deliveryAdress']['city'], $data['deliveryAdress']['zip'], $data['deliveryAdress']['country'], $data['deliveryTerms']['note']);
	}
	
	private function nullEmptyNullableElements(Array $data): Array
	{
		$data['deliveryAdress']['streetAndNumber'] = $data['deliveryAdress']['streetAndNumber'] !== "" ? $data['deliveryAdress']['streetAndNumber'] : null;
		$data['deliveryAdress']['city'] = $data['deliveryAdress']['city'] !== "" ? $data['deliveryAdress']['city'] : null;
		$data['deliveryAdress']['zip'] = $data['deliveryAdress']['zip'] !== "" ? $data['deliveryAdress']['zip'] : null;
		$data['deliveryAdress']['country'] = $data['deliveryAdress']['country'] !== "" && $data['personalData']['differentAdress'] ? $data['deliveryAdress']['country'] : null;
		$data['deliveryTerms']['note'] = $data['deliveryTerms']['note'] !== "" ? $data['deliveryTerms']['note'] : null;

		return $data;
	}
	
	private function findCountryForCustomerData($countryId): Country
	{
		return $this->countryRepository->findById(intval($countryId));
	}
}
