<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Nette;
use App\Entity\BasketItem;
use App\Services\Repository\BaseRepository;
use App\Services\Repository\RepositoryInterface\IPurchaseRepository;
use App\Services\Repository\RepositoryInterface\ICountryRepository;
use App\Services\Repository\RepositoryInterface\IPurchaseItemRepository;
use App\Services\Repository\RepositoryInterface\IPurchaseStatusRepository;
use App\Services\Repository\RepositoryInterface\IEntityRepository;
use App\Services\Repository\RepositoryInterface\ICreatableAndDeleteableEntityRepository;
use App\Entity\Purchase;
use App\Entity\Factory\PurchaseFactory;
use App\Entity\Factory\PurchaseItemFactory;


final class PurchaseRepository extends BaseRepository implements ICreatableAndDeleteableEntityRepository, IPurchaseRepository
{
	private const ENTITY_IDENTIFICATION = '8 purchase';
	private $entityRepository;
	private $database;
	private $purchaseItemRepository;
	private $purchaseStatusRepository;
	private $countryRepository;
	
	public function __construct(IEntityRepository $entityRepository, Nette\Database\Context $database, IPurchaseItemRepository $purchaseItemRepository, IPurchaseStatusRepository $purchaseStatusRepository, ICountryRepository $countryRepository){
		$this->entityRepository = $entityRepository;
		$this->database = $database;
		$this->purchaseItemRepository = $purchaseItemRepository;
		$this->purchaseStatusRepository = $purchaseStatusRepository;
		$this->countryRepository = $countryRepository;
	}
	
	public function findAll(): Array
	{
		$queryResult = $this->database->query("
			SELECT * 
			FROM purchase
			ORDER BY created_at DESC
		");
		
		$arrayOfPurchases = $this->queryResultToArrayOfObjects($queryResult);
		
		return $arrayOfPurchases;
	}

	public function findById(int $id)
	{}

	public function insert($purchase)
	{
		try{
			$purchase->setId($this->generateNewId($this->getUsedIds(), $this->entityRepository->find(self::ENTITY_IDENTIFICATION)));
		} catch(\Exception $ex){
			throw $ex;
		}

		$purchase->setPurchaseStatus($this->purchaseStatusRepository->findDefaultStatusForNewPurchases());
		$purchase->setCreatedAt(new \DateTime('now'));

		$howDidItGo = $this->database->query("
			INSERT INTO purchase
			", $purchase->toArray());

		return [
			'id' => $purchase->getId(),
			'rowCount' => $howDidItGo->getRowCount()
			];
	}
	
	public function update($object)
	{}
	public function delete(string $identification)
	{}

	private function getUsedIds(): array
	{
		$usedIds = $this->database
			->query("
				SELECT id
				FROM purchase
			")
			->fetchPairs();
		
		return $usedIds;
	}
	
	private function queryResultToArrayOfObjects($queryResult): Array
	{
		$arrayOfPurchaseObjects = [];
		
		while($row = $queryResult->fetch()){
			$row->customerCountry = $this->countryRepository->findById($row->customerCountry);
			$row->deliveryCountry = $this->countryRepository->findById($row->deliveryCountry);
			$row->purchaseStatus = $this->purchaseStatusRepository->findById($row->purchasestatus_id);
			$row->purchaseItems = $this->purchaseItemRepository->findByPurchaseId($row->id);
			$arrayOfPurchaseObjects[] = PurchaseFactory::createFromObject($row);
		}
		
		return $arrayOfPurchaseObjects;
	}
}
