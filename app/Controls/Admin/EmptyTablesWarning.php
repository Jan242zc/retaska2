<?php

namespace App\Controls\Admin;

use Nette; 
use Nette\Application\UI\Control;
use App\Services\Repository\RepositoryInterface\IPurchaseStatusRepository;
use App\Services\Repository\RepositoryInterface\IDeliveryCountryPaymentPricesRepository;


final class EmptyTablesWarning extends Control
{
	private const CANNOT_RECEIVE_PURCHASES_WARNING = 'Pozor, nelze přijímat objednávky! ';
	private const NO_PURCHASE_STATUSES_WARNING = 'Je nutné vytvořit alespoň jeden stav objednávky a nastavit jej jako výchozí ve správě stavů objednávek.';
	private const NO_DELIVERY_SERVICES = 'Je nutné vytvořit dopravní služby (tzn. min. jednu, pokud bude nezávislá na státu) ve správě dopravních služeb (případně i možnosti dopravy a druhy plateb).';

	/** @var IPurchaseStatusRepository */
	private $purchaseStatusRepository;
	
	/** @var IDeliveryCountryPaymentPricesRepository */
	private $deliveryCountryPaymentPricesRepository;
	
	public function __construct(IPurchaseStatusRepository $purchaseStatusRepository, IDeliveryCountryPaymentPricesRepository $deliveryCountryPaymentPricesRepository){
		$this->purchaseStatusRepository = $purchaseStatusRepository;
		$this->deliveryCountryPaymentPricesRepository = $deliveryCountryPaymentPricesRepository;
	}

	public function render(): void
	{
		$this->template->warnings = [];
		$this->template->warnings[] = $this->generatePurchaseStatusWarning();
		
		$this->template->render(__DIR__ . '/templates/emptyTablesWarning.latte');
	}
	
	public function generatePurchaseStatusWarning()
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
}