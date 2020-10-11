<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Repository\RepositoryInterface\IDeliveryCountryPaymentPricesRepository;


final class DeliveryCountryPaymentPricesArrayGenerator
{
	/** @var IDeliveryCountryPaymentPricesRepository */
	private $deliveryCountryPaymentPricesRepository;
	
	public function __construct(IDeliveryCountryPaymentPricesRepository $deliveryCountryPaymentPricesRepository){
		$this->deliveryCountryPaymentPricesRepository = $deliveryCountryPaymentPricesRepository;
	}
	
	public function generateTheArray(): Array
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
			$deliveryServices[$deliveryService['delivery']][$deliveryService['country'] ?? 'N/A']['payment'][] = $deliveryService['paymentPrice'];
		}
		
		return $deliveryServices;
	}
}
