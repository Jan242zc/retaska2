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
	{}

	public function findById(int $id)
	{}

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

	public function update($object)
	{}

	public function delete(string $identification)
	{}

	public function find(string $identification)
	{}

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
	{}
	
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
