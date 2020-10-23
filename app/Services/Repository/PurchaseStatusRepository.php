<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Nette;
use App\Services\Repository\BaseRepository;
use App\Services\Repository\RepositoryInterface\IPurchaseStatusRepository;
use App\Services\Repository\RepositoryInterface\ICreatableAndDeleteableEntityRepository;
use App\Services\Repository\RepositoryInterface\INameableEntityRepository;
use App\Services\Repository\RepositoryInterface\ISelectableEntityRepository;
use App\Entity\Factory\PurchaseStatusFactory;
use App\Services\Repository\RepositoryInterface\IEntityRepository;


final class PurchaseStatusRepository extends BaseRepository implements ICreatableAndDeleteableEntityRepository, INameableEntityRepository, ISelectableEntityRepository, IPurchaseStatusRepository
{
	private const ENTITY_IDENTIFICATION = '7 purchasestatus';
	
	private $entityRepository;
	private $database;

	public function __construct(IEntityRepository $entityRepository, Nette\Database\Context $database){
		$this->database = $database;
		$this->entityRepository = $entityRepository;
	}

	public function findAll(): Array
	{
		$queryResult = $this->database
			->query("
				SELECT *
				FROM purchasestatus
			");
		
		$arrayOfCountries = [];		
		while($row = $queryResult->fetch()){
			$arrayOfPurchaseStatuses[] = PurchaseStatusFactory::createFromObject($row);
		}
		
		return $arrayOfPurchaseStatuses;
	}

	public function find(string $identification)
	{
		$identification = $this->chopIdentification($identification);
		
		$queryResult = $this->database
			->query("
				SELECT *
				FROM purchasestatus
				WHERE id = ? AND name = ?
				", $identification['id'], $identification['name']
				)
			->fetch();

		if(is_null($queryResult)){
			throw new \Exception('No purchase status found.');
		}
		
		return $purchaseStatus = PurchaseStatusFactory::createFromObject($queryResult);
	}

	public function findById(int $id)
	{
		$queryResult = $this->database
			->query("
				SELECT *
				FROM purchasestatus
				WHERE id = ?
				", $id)
			->fetch();

		if(!is_null($queryResult)){
			return $purchaseStatus = PurchaseStatusFactory::createFromObject($queryResult);
		}
		
		return $queryResult;
	}

	public function insert($purchaseStatus)
	{
		try{
			$purchaseStatus->setId($this->generateNewId($this->getUsedIds(), $this->entityRepository->find(self::ENTITY_IDENTIFICATION)));
		} catch(\Exception $ex){
			throw $ex;
		}
		
		$howDidItGo = $this->database->query("
			INSERT INTO purchasestatus
			", $purchaseStatus->toArray());
			
		return $howDidItGo->getRowCount();
	}

	public function update($purchaseStatus)
	{
		$id = $purchaseStatus->getId();
		$purchaseStatusArray = $purchaseStatus->toArray();
		unset($purchaseStatusArray['id']);
		
		$howDidItGo = $this->database->query("
			UPDATE purchasestatus
			SET", $purchaseStatusArray, "
			WHERE id = ?", $id);
		
		return $howDidItGo->getRowCount();
	}

	public function delete(string $identification)
	{
		$identification = $this->chopIdentification($identification);
		
		$howDidItGo = $this->database->query("
			DELETE FROM purchasestatus
			WHERE id = ? AND name = ?
		", $identification['id'], $identification['name']);
		
		return $howDidItGo->getRowCount();
	}

	public function getArrayOfUsedNames($currentPurchaseStatusId = null): Array
	{
		if(is_null($currentPurchaseStatusId)){
			$usedNames = $this->database
				->query("
					SELECT name
					FROM purchasestatus
				")
				->fetchPairs();		
		} else {
			$usedNames = $this->database
				->query("
					SELECT name
					FROM purchasestatus
					WHERE id != ?
				", $currentPurchaseStatusId)
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
				FROM purchasestatus
			")
			->fetchPairs();
			
		return $queryResult;
	}

	public function findAllForNewDefaultForm(): Array
	{
		$queryResult = $this->database
			->query("
				SELECT *
				FROM purchasestatus
				WHERE means_cancelled = 0
			")
			->fetchPairs();

		return $queryResult;
	}

	public function setDefaultStatusForNewPurchases($purchaseStatus)
	{
		$this->database->query("
			UPDATE purchasestatus
			SET default_for_new_purchases = 0
		");

		$howDidItGo = $this->database->query("
			UPDATE purchasestatus
			SET default_for_new_purchases = 1
			WHERE id = ? AND name = ? AND means_cancelled = 0
		", $purchaseStatus->getId(), $purchaseStatus->getName());

		return $howDidItGo->getRowCount();
	}

	private function getUsedIds(): array
	{
		$usedIds = $this->database
			->query("
				SELECT id
				FROM purchasestatus
			")
			->fetchPairs();
		
		return $usedIds;
	}
}
