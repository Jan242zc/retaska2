<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\GeneralServiceInterface\IDatabaseReadinessChecker;
use App\Services\Repository\RepositoryInterface\IPurchaseStatusRepository;
use App\Services\Repository\RepositoryInterface\IDeliveryCountryPaymentPricesRepository;
use App\Services\Repository\RepositoryInterface\ICountryRepository;


final class DatabaseReadinessChecker implements IDatabaseReadinessChecker
{
	/** @var IPurchaseStatusRepository */ 
	private $purchaseStatusRepository;
	
	/** @var IDeliveryCountryPaymentPricesRepository */
	private $deliveryCountryPaymentPricesRepository;
	
	/** @var ICountryRepository */
	private $countryRepository;
	
	public function __construct(IPurchaseStatusRepository $purchaseStatusRepository, IDeliveryCountryPaymentPricesRepository $deliveryCountryPaymentPricesRepository, ICountryRepository $countryRepository){
		$this->purchaseStatusRepository = $purchaseStatusRepository;
		$this->deliveryCountryPaymentPricesRepository = $deliveryCountryPaymentPricesRepository;
		$this->countryRepository = $countryRepository;
	}
	
	public function databaseIsReadyToReceivePurchases(): bool
	{
		if(!$this->purchaseStatusRepository->findAll()){
			return false;
		}
		try{
			$this->purchaseStatusRepository->findDefaultStatusForNewPurchases();
		} catch(\Exception $ex){
			return false;
		}
		if(!$this->deliveryCountryPaymentPricesRepository->findAll()){
			return false;
		}
		if(!$this->countryRepository->findAll()){
			return false;
		}
		return true;
	}
}
