<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Nette;
use App\Entity\Payment;
use App\Entity\Factory\PaymentFactory;
use App\Services\Repository\BaseRepository;
use App\Services\Repository\RepositoryInterface\IRepository;
use App\Services\Repository\RepositoryInterface\ICreatableAndDeleteableEntityRepository;
use App\Services\Repository\RepositoryInterface\IPaymentRepository;
use App\Services\Repository\RepositoryInterface\IEntityRepository;

final class PaymentRepository extends BaseRepository implements ICreatableAndDeleteableEntityRepository, IPaymentRepository
{
	private const ENTITY_IDENTIFICATION = '4 payment';
	private $entityRepository;
	private $database;

	public function __construct(IEntityRepository $entityRepository, Nette\Database\Context $database){
		$this->entityRepository = $entityRepository;
		$this->database = $database;
	}
	
	public function findAll(): Array
	{
		$queryResult = $this->database
			->query("
				SELECT *
				FROM payment
			");
		
		$arrayOfCountries = [];		
		while($row = $queryResult->fetch()){
			$arrayOfCountries[] = PaymentFactory::createFromObject($row);
		}
		
		return $arrayOfCountries;
	}
	
	public function find(string $identification)
	{
		$identification = $this->chopIdentification($identification);
		
		$queryResult = $this->database
			->query("
				SELECT *
				FROM payment
				WHERE id = ? AND name = ?
				", $identification['id'], $identification['name']
				)
			->fetch();
		
		if(is_null($queryResult)){
			throw new \Exception('No payment found.');
		}
		
		return $payment = PaymentFactory::createFromObject($queryResult);
	}
	
	public function findById(int $id)
	{
		$queryResult = $this->database
			->query("
				SELECT *
				FROM payment
				WHERE id = ?
				", $id)
			->fetch();

		if(!is_null($queryResult)){
			return $payment = PaymentFactory::createFromObject($queryResult);
		}
		
		return $queryResult;
	}
	
	public function insert($payment)
	{
		try{
			$payment->setId($this->generateNewId($this->getUsedIds(), $this->entityRepository->find(self::ENTITY_IDENTIFICATION)));
		} catch(\Exception $ex){
			throw $ex;
		}
		
		$howDidItGo = $this->database->query("
			INSERT INTO payment
			", $payment->toArray());
			
		return $howDidItGo->getRowCount();
	}
	
	public function update($payment)
	{
		$id = $payment->getId();
		$paymentArray = $payment->toArray();
		unset($paymentArray['id']);
		
		$howDidItGo = $this->database->query("
			UPDATE payment
			SET", $paymentArray, "
			WHERE id = ?", $id);
		
		return $howDidItGo->getRowCount();
	}
	
	public function delete(string $identification)
	{
		$identification = $this->chopIdentification($identification);
		
		$howDidItGo = $this->database->query("
			DELETE FROM payment
			WHERE id = ? AND name = ?
		", $identification['id'], $identification['name']);
		
		return $howDidItGo->getRowCount();
	}

	private function getUsedIds(): array
	{
		$usedIds = $this->database
			->query("
				SELECT id
				FROM payment
			")
			->fetchPairs();
		
		return $usedIds;
	}

	public function getArrayOfUsedNames($currentPaymentId = null): Array
	{
		if(is_null($currentPaymentId)){
			$usedNames = $this->database
				->query("
					SELECT name
					FROM payment
				")
				->fetchPairs();		
		} else {
			$usedNames = $this->database
				->query("
					SELECT name
					FROM payment
					WHERE id != ?
				", $currentPaymentId)
				->fetchPairs();
		}
		
		for($i = 0; $i < count($usedNames); $i++){
			$usedNames[$i] = mb_strtolower($usedNames[$i]);
		}
		return $usedNames;
	}
	
	public function findAllForForm(): Array
	{
		$queryResult = $this->database
			->query("
				SELECT *
				FROM payment
			")
			->fetchPairs();
			
		return $queryResult;
	}
}

