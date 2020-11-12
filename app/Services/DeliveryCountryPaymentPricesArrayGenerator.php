<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Repository\RepositoryInterface\IDeliveryCountryPaymentPricesRepository;
use App\Services\GeneralServiceInterface\IDeliveryCountryPaymentPricesArrayGenerator;
use App\Services\Repository\RepositoryInterface\ICountryRepository;


//generates arrays for order form (delivery possibilities, payment methods and their respective prices)
final class DeliveryCountryPaymentPricesArrayGenerator implements IDeliveryCountryPaymentPricesArrayGenerator
{
	/** @var IDeliveryCountryPaymentPricesRepository */
	private $deliveryCountryPaymentPricesRepository;
	
	/** @var ICountryRepository */
	private $countryRepository;
	
	public function __construct(IDeliveryCountryPaymentPricesRepository $deliveryCountryPaymentPricesRepository, ICountryRepository $countryRepository){
		$this->deliveryCountryPaymentPricesRepository = $deliveryCountryPaymentPricesRepository;
		$this->countryRepository = $countryRepository;
	}
	
	public function generateByDeliveryArray(): Array
	{
		$deliveryServicesData = [];
		
		foreach($this->deliveryCountryPaymentPricesRepository->findAll() as $deliveryService){
			$deliveryServicesData[] = $deliveryService->toArray();
		}
		
		$deliveryServices = [];
		
		foreach($deliveryServicesData as $deliveryService){
			if(!in_array($deliveryService['delivery'], $deliveryServices)){				
				$deliveryServices[$deliveryService['delivery']] = [];
			}
		}
		
		foreach($deliveryServicesData as $deliveryService){
			$deliveryServices[$deliveryService['delivery']][$deliveryService['country'] ?? 'N/A'] = ['price' => $deliveryService['deliveryPrice'], 'payment' => []];
		}
		
		foreach($deliveryServicesData as $deliveryService){
			$deliveryServices[$deliveryService['delivery']][$deliveryService['country'] ?? 'N/A']['payment'][$deliveryService['payment']] = $deliveryService['paymentPrice'];
		}
		
		return $deliveryServices;
	}
	
	public function generateByCountryArray(): Array
	{
		$deliveryServicesData = [];		

		//split country independent from country specific
		foreach($this->deliveryCountryPaymentPricesRepository->findAll() as $deliveryService){
			if(!is_null($deliveryService->getCountry())){
				$deliveryServicesData[] = $deliveryService->toArray();
			}
		}

		$deliveryServices = [];
		
		foreach($deliveryServicesData as $deliveryService){
			if(!in_array($deliveryService['country'], $deliveryServices)){				
				$deliveryServices[$deliveryService['country']] = [];
			}
		}

		foreach($deliveryServicesData as $deliveryService){
			$deliveryServices[$deliveryService['country']][$deliveryService['delivery']] = ['price' => $deliveryService['deliveryPrice'], 'payment' => []];
		}

		foreach($deliveryServicesData as $deliveryService){
			$deliveryServices[$deliveryService['country']][$deliveryService['delivery']]['payment'][$deliveryService['payment']] = $deliveryService['paymentPrice'];
		}

		return $deliveryServices;
	}
	
	public function generateCountryIndependentServicesArray(): Array
	{
		$countryIndependentServicesData = [];		
		
		//split country independent from country specific
		foreach($this->deliveryCountryPaymentPricesRepository->findAll() as $deliveryService){
			if(is_null($deliveryService->getCountry())){
				$countryIndependentServicesData[] = $deliveryService->toArray();
			}
		}

		$countryIndependentServices = [];

		foreach($countryIndependentServicesData as $deliveryService){
			if(!in_array($deliveryService['delivery'], $countryIndependentServices)){				
				$countryIndependentServices[$deliveryService['delivery']] = [];
			}
		}

		foreach($countryIndependentServicesData as $deliveryService){
			$countryIndependentServices[$deliveryService['delivery']] = ['price' => $deliveryService['deliveryPrice'], 'payment' => []];
		}

		foreach($countryIndependentServicesData as $deliveryService){
			$countryIndependentServices[$deliveryService['delivery']]['payment'][$deliveryService['payment']] = $deliveryService['paymentPrice'];
		}

		return $countryIndependentServices;
	}
	
	public function generateCountriesWithServicesArray(): Array
	{
		$countryNames = $this->countryRepository->findAllForForm();
		$coutriesWithDeliveryServicesArray = [];
		foreach($this->generateByCountryArray() as $countryId => $services){
			$coutriesWithDeliveryServicesArray[$countryId] = $countryNames[$countryId];
		}
		
		return $coutriesWithDeliveryServicesArray;
	}
}
