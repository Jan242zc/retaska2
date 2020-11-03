<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\DeliveryCountryPaymentPrices;
use App\Entity\Delivery;
use App\Entity\Country;
use App\Entity\Payment;
use App\Services\Repository\RepositoryInterface\IDeliveryRepository;
use App\Services\Repository\RepositoryInterface\ICountryRepository;
use App\Services\Repository\RepositoryInterface\IPaymentRepository;


final class DeliveryCountryPaymentPricesFactory
{
	/** @var IDeliveryRepository */
	private $deliveryRepository;
	
	/** @var ICountryRepository */
	private $countryRepository;
	
	/** @var IPaymentRepository */
	private $paymentRepository;
	
	public function __construct(IDeliveryRepository $deliveryRepository, ICountryRepository $countryRepository, IPaymentRepository $paymentRepository){
		$this->deliveryRepository = $deliveryRepository;
		$this->countryRepository = $countryRepository;
		$this->paymentRepository = $paymentRepository;
	}

	public function createFromArray(Array $data): DeliveryCountryPaymentPrices
	{
		try{
			$data['delivery'] = $this->findDelivery($data['delivery']);
			$data['payment'] = $this->findPayment($data['payment']);
			if(!is_null($data['country'])){
				$data['country'] = $this->findCountry($data['country']);
			}
		} catch(\Exception $ex){
			throw $ex;
		}

		if(!$data['id']){			
			return new DeliveryCountryPaymentPrices(null, $data['delivery'], $data['payment'], $data['deliveryPrice'], $data['paymentPrice'], $data['country']);
		} else {			
			return new DeliveryCountryPaymentPrices($data['id'], $data['delivery'], $data['payment'], $data['deliveryPrice'], $data['paymentPrice'], $data['country']);
		}
	}

	public function createFromObject($object): DeliveryCountryPaymentPrices
	{
		try{
			$object->delivery = $this->findDelivery($object->delivery);
			$object->payment = $this->findPayment($object->payment);
			if(!is_null($object->country)){
				$object->country = $this->findCountry($object->country);
			}
		} catch(\Exception $ex){
			throw $ex;
		}

		if(!$object->id){			
			return new DeliveryCountryPaymentPrices(null, $object->delivery, $object->payment, $object->deliveryPrice, $object->paymentPrice, $object->country);
		} else {
			return new DeliveryCountryPaymentPrices($object->id, $object->delivery, $object->payment, $object->deliveryPrice, $object->paymentPrice, $object->country);			
		}
	}

	private function findDelivery($deliveryId): Delivery
	{
		try{
			return $this->deliveryRepository->findById(intval($deliveryId));
		} catch(\Exception $ex){
			throw $ex;
		}
	}

	private function findCountry($countryId): Country
	{
		try{
			return $this->countryRepository->findById(intval($countryId));
		} catch(\Exception $ex){
			throw $ex;
		}
	}

	private function findPayment($paymentId): Payment
	{
		try{
			return $this->paymentRepository->findById(intval($paymentId));
		} catch(\Exception $ex){
			throw $ex;
		}
	}
}
