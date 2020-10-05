<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Nette;
use App\Entity\Factory\DeliveryFactory;
use App\Entity\Factory\CountryFactory;
use App\Entity\Factory\PaymentFactory;
use App\Entity\Factory\DeliveryCountryPaymentPricesFactory;
use App\Entity\DeliveryCountryPaymentPrices;
use App\Services\Repository\BaseRepository;
use App\Services\Repository\RepositoryInterface\IRepository;
use App\Services\Repository\RepositoryInterface\ICreatableAndDeleteableEntityRepository;
use App\Services\Repository\RepositoryInterface\IEntityRepository;
use App\Services\Repository\RepositoryInterface\IDeliveryRepository;
use App\Services\Repository\RepositoryInterface\ICountryRepository;
use App\Services\Repository\RepositoryInterface\IPaymentRepository;
use App\Services\Repository\RepositoryInterface\IDeliveryCountryPaymentPricesRepository;


final class DeliveryCountryPaymentPricesRepository extends BaseRepository implements ICreatableAndDeleteableEntityRepository, IDeliveryCountryPaymentPricesRepository
{
	private const ENTITY_IDENTIFICATION = '6 deliverycountrypaymentprices';
	private $entityRepository;
	private $database;
	private $deliveryRepository;
	private $countryRepository;
	private $paymentRepository;
	private $deliveryCountryPaymentPricesRepository;

	public function __construct(IEntityRepository $entityRepository, Nette\Database\Context $database, IDeliveryRepository $deliveryRepository, ICountryRepository $countryRepository, IPaymentRepository $paymentRepository){
		$this->entityRepository = $entityRepository;
		$this->database = $database;
		$this->deliveryRepository = $deliveryRepository;
		$this->countryRepository = $countryRepository;
		$this->paymentRepository = $paymentRepository;
	}
	
	public function findAll(): Array
	{
		$queryResult = $this->database
			->query("
				SELECT *
				FROM deliverycountrypaymentprices
			");
		
		try{
			$arrayOfDCPPs = $this->queryResultToArrayOfObjects($queryResult);
		} catch (\Exception $ex){
			throw $ex;
		}
		
		return $arrayOfDCPPs;
	}
	
	public function find(string $identification)
	{
		
	}
	
	public function findById(int $id): DeliveryCountryPaymentPrices
	{	
		$queryResult = $this->database
			->query("
				SELECT *
				FROM deliverycountrypaymentprices
				WHERE id = ?
				", $id)
			->fetch();
		
		//as the $identification variable should always be valid (one should always find a category with given id and name), I decided to throw exception in case nothing is found rather than return null
		if(is_null($queryResult)){ 
			throw new \Exception('Nothing found.');
		}
		
		try{
			$queryResult->delivery = $this->deliveryRepository->findById($queryResult->delivery);
			$queryResult->payment = $this->paymentRepository->findById($queryResult->payment);
			if(isset($queryResult->country)){
				$queryResult->country = $this->countryRepository->findById($queryResult->country);
			}
		} catch (\Exception $ex) {
			throw $ex;
		}
		
		return DeliveryCountryPaymentPricesFactory::createFromObject($queryResult);
	}
	
	public function insert($deliverycountrypaymentprices)
	{
		try{
			$deliverycountrypaymentprices->setId($this->generateNewId($this->getUsedIds(), $this->entityRepository->find(self::ENTITY_IDENTIFICATION)));
		} catch(\Exception $ex){
			throw $ex;
		}
		
		$howDidItGo = $this->database->query("
			INSERT INTO deliverycountrypaymentprices
			", $deliverycountrypaymentprices->toArray());
			
		return $howDidItGo->getRowCount();
	}
	
	public function update($deliverycountrypaymentprices)
	{
		$id = $deliverycountrypaymentprices->getId();
		$deliverycountrypaymentpricesArray = $deliverycountrypaymentprices->toArray();
		unset($deliverycountrypaymentpricesArray['id']);
		
		$howDidItGo = $this->database->query("
			UPDATE deliverycountrypaymentprices
			SET", $deliverycountrypaymentpricesArray, "
			WHERE id = ?", $id);
		
		return $howDidItGo->getRowCount();
	}
	
	public function delete(string $identification)
	{		
		$howDidItGo = $this->database->query("
			DELETE FROM deliverycountrypaymentprices
			WHERE id = ?
		", $identification);
		
		return $howDidItGo->getRowCount();
	}

	private function getUsedIds(): array
	{
		$usedIds = $this->database
			->query("
				SELECT id
				FROM deliverycountrypaymentprices
			")
			->fetchPairs();
		
		return $usedIds;
	}
	
	private function queryResultToArrayOfObjects($queryResult): Array
	{
		$arrayOfProducts = [];
		while($row = $queryResult->fetch()){
			//I chose this over JOIN as no extension of the category entity will require no subsequent changes of this method
			try{
				$row->delivery = $this->deliveryRepository->findById($row->delivery); 
				$row->payment = $this->paymentRepository->findById($row->payment);
				if(!is_null($row->country)){
					$row->country = $this->countryRepository->findById($row->country);					
				}
			} catch (\Exception $ex){
				throw $ex;
			}
			
			$arrayOfProducts[] = DeliveryCountryPaymentPricesFactory::createFromObject($row);
		}
		
		return $arrayOfProducts;
	}
}
