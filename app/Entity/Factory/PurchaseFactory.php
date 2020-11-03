<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\Purchase;
use App\Entity\PurchaseStatus;
use App\Entity\Country;
use App\Entity\CustomerData;
use App\Entity\Basket;
use App\Entity\Factory\PurchaseItemFactory;
use App\Services\Repository\RepositoryInterface\IPurchaseStatusRepository;
use App\Services\Repository\RepositoryInterface\ICountryRepository;
use App\Services\Repository\RepositoryInterface\IPurchaseItemRepository;


final class PurchaseFactory
{
	/** @var IPurchaseStatusRepository */
	private $purchaseStatusRepository;
	
	/** @var ICountryRepository */
	private $countryRepository;
	
	/** @var PurchaseItemFactory */
	private $purchaseItemFactory;
	
	/** @var IPurchaseItemRepository */
	private $purchaseItemRepository;
	
	public function __construct(IPurchaseStatusRepository $purchaseStatusRepository, ICountryRepository $countryRepository, PurchaseItemFactory $purchaseItemFactory, IPurchaseItemRepository $purchaseItemRepository){
		$this->purchaseStatusRepository = $purchaseStatusRepository;
		$this->countryRepository = $countryRepository;
		$this->purchaseItemFactory = $purchaseItemFactory;
		$this->purchaseItemRepository = $purchaseItemRepository;
	}
	
	public function createFromArray(array $data): Purchase
	{
		if($data['id']){
			$id = $data['id'];
		} else {
			$id = null;
		}
		
		$data = $this->nullNullableInArray($data);
		
		if($data['shipToOtherThanCustomerAdress']){
			$shipToOtherThanCustomerAdress = true;
		} else {
			$shipToOtherThanCustomerAdress = false;
		}
		
		try{
			$data['purchaseStatus'] = $this->findPurchaseStatusForPurchase($data['purchaseStatus']);
			$data['customerCountry'] = $this->findCountryForPurchase($data['customerCountry']);
			if(!is_null($data['deliveryCountry'])){
				$data['deliveryCountry'] = $this->findCountryForPurchase($data['deliveryCountry']);
			}
		} catch (\Exception $ex) {
			throw $ex;
		}
		
		return new Purchase($id, $data['customerName'], $data['customerStreetAndNumber'], $data['customerCity'], $data['customerZip'], $data['customerCountry'], $data['email'], $data['phone'], $data['deliveryName'], $data['deliveryPrice'], $data['paymentName'], $data['paymentPrice'], $data['totalPrice'], $shipToOtherThanCustomerAdress, $data['created_at'], $data['purchaseStatus'], $data['purchaseItems'], $data['deliveryStreetAndNumber'], $data['deliveryCity'], $data['deliveryZip'], $data['deliveryCountry'], $data['note']);
	}
	
	public function createFromObject($object): Purchase
	{
		if($object->id){
			$id = $object->id;
		} else {
			$id = null;
		}
		
		$object = $this->nullNullableInObject($object);
		
		if($object->shipToOtherThanCustomerAdress){
			$shipToOtherThanCustomerAdress = true;
		} else {
			$shipToOtherThanCustomerAdress = false;
		}
		
		try{
			$object->purchaseStatus = $this->findPurchaseStatusForPurchase($object->purchasestatus_id);
			$object->customerCountry = $this->findCountryForPurchase($object->customerCountry);
			if(!is_null($object->deliveryCountry)){
				$object->deliveryCountry = $this->findCountryForPurchase($object->deliveryCountry);
			}
			if(!is_null($object->id)){
				$object->purchaseItems = $this->purchaseItemRepository->findByPurchaseId(intval($object->id));
			}
		} catch (\Exception $ex){
			throw $ex;
		}
	
		return new Purchase($id, $object->customerName, $object->customerStreetAndNumber, $object->customerCity, $object->customerZip, $object->customerCountry, $object->email, $object->phone, $object->deliveryName, $object->deliveryPrice, $object->paymentName, $object->paymentPrice, $object->totalPrice, $shipToOtherThanCustomerAdress, $object->created_at, $object->purchaseStatus, $object->purchaseItems, $object->deliveryStreetAndNumber, $object->deliveryCity, $object->deliveryZip, $object->deliveryCountry, $object->note);
	}
	
	private function nullNullableInArray(array $data): array
	{
		$data['deliveryStreetAndNumber'] = $data['deliveryStreetAndNumber'] === '' ? null : $data['deliveryStreetAndNumber'];
		$data['deliveryCity'] = $data['deliveryCity'] === '' ? null : $data['deliveryCity'];
		$data['deliveryZip'] = $data['deliveryZip'] === '' ? null : $data['deliveryZip'];
		$data['deliveryCountry'] = $data['deliveryCountry'] === '' ? null : $data['deliveryCountry'];
		$data['note'] = $data['note'] === '' ? null : $data['note'];
		
		return $data;
	}
	
	private function nullNullableInObject($object)
	{
		$object->deliveryStreetAndNumber = $object->deliveryStreetAndNumber === '' ? null : $object->deliveryStreetAndNumber;
		$object->deliveryCity = $object->deliveryCity === '' ? null : $object->deliveryCity;
		$object->deliveryZip = $object->deliveryZip === '' ? null : $object->deliveryZip;
		$object->deliveryCountry = $object->deliveryCountry === '' ? null : $object->deliveryCountry;
		$object->note = $object->note === '' ? null : $object->note;
		
		return $object;
	}
	
	public function createFromCustomerData(CustomerData $customerData, float $totalPrice, array $basketItems): Purchase
	{
		$purchaseItems = $this->purchaseItemFactory->createFromBasketData($basketItems);
		
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

	private function findPurchaseStatusForPurchase($purchaseStatusId): PurchaseStatus
	{
		try{
			return $purchaseStatus = $this->purchaseStatusRepository->findById(intval($purchaseStatusId));
		} catch (\Exception $ex){
			throw $ex;
		}
	}
	
	private function findCountryForPurchase($countryId): Country
	{
		try{
			return $country = $this->countryRepository->findById(intval($countryId));
		} catch (\Exception $ex){
			throw $ex;
		}
	}
}
