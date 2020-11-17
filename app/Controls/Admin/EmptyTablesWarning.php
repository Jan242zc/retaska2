<?php

namespace App\Controls\Admin;

use Nette; 
use Nette\Application\UI\Control;
use App\Services\Repository\RepositoryInterface\IPurchaseStatusRepository;
use App\Services\Repository\RepositoryInterface\IDeliveryCountryPaymentPricesRepository;
use App\Services\Repository\RepositoryInterface\ICountryRepository;


final class EmptyTablesWarning extends Control
{
	private const CANNOT_RECEIVE_PURCHASES_WARNING = 'Pozor, nelze přijímat objednávky! ';
	private const NO_PURCHASE_STATUSES_WARNING = 'Je nutné vytvořit alespoň jeden stav objednávky a nastavit jej jako výchozí ve správě stavů objednávek.';
	private const NO_DELIVERY_SERVICES = 'Je nutné vytvořit dopravní služby (tzn. min. jednu, pokud bude nezávislá na státu) ve správě dopravních služeb (případně i možnosti dopravy a druhy plateb).';
	private const NO_COUNTRIES = 'Je třeba ve správě států zadat státy pro účely fakturačních adres a případně i dopravních služeb.';
	private const NO_COUNTRY_DEPENDENT_DELIVERY_SERVICES = 'V databázi nejsou založeny dopravní služby, které by zákazníkovi umožňovaly nechat si zboží doručit.';

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

	public function render(): void
	{
		$this->template->warnings = [];
		if($purchaseStatusWarning = $this->generatePurchaseStatusWarning()){
			$this->template->warnings[] = $purchaseStatusWarning;
		}
		
		if($deliveryServicesWarning = $this->generateDeliveryServicesWarning()){
			$this->template->warnings[] = $deliveryServicesWarning;
		}
		
		if($countryWarning = $this->generateCountryWarning()){
			$this->template->warnings[] = $countryWarning;
		}
		
		if($noCountryDependentWarning = $this->generateNoCountryIndependentDeliveryServiceWarning()){
			$this->template->warnings[] = $noCountryDependentWarning;
		}
		
		$this->template->render(__DIR__ . '/templates/emptyTablesWarning.latte');
	}
	
	private function generatePurchaseStatusWarning()
	{
		$noPurchaseStatusExist = !$this->purchaseStatusRepository->findAll();
		try{
			$this->purchaseStatusRepository->findDefaultStatusForNewPurchases();
			$noDefaultPurchaseStatusExists = false;
		} catch(\Exception $ex){
			$noDefaultPurchaseStatusExists = true;			
		}
		if($noPurchaseStatusExist || $noDefaultPurchaseStatusExists){
			return self::CANNOT_RECEIVE_PURCHASES_WARNING . self::NO_PURCHASE_STATUSES_WARNING;
		}
	}
	
	private function generateDeliveryServicesWarning()
	{
		if(!$this->deliveryCountryPaymentPricesRepository->findAll()){
			return self::CANNOT_RECEIVE_PURCHASES_WARNING . self::NO_DELIVERY_SERVICES;
		}
	}
	
	private function generateCountryWarning()
	{
		if(!$this->countryRepository->findAll()){
			return self::CANNOT_RECEIVE_PURCHASES_WARNING . self::NO_COUNTRIES;
		}
	}
	
	private function generateNoCountryIndependentDeliveryServiceWarning()
	{
		if(!$this->deliveryCountryPaymentPricesRepository->findCountryDependent()){
			return self::NO_COUNTRY_DEPENDENT_DELIVERY_SERVICES;
		}
	}
}