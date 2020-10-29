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
use App\Services\Repository\RepositoryInterface\IProductRepository;
use App\Services\Repository\RepositoryInterface\ICreatableAndDeleteableEntityRepository;
use App\Entity\Purchase;
use App\Entity\Factory\PurchaseFactory;
use App\Entity\Factory\PurchaseItemFactory;


final class PurchaseRepository extends BaseRepository implements ICreatableAndDeleteableEntityRepository, IPurchaseRepository
{
	private const ENTITY_IDENTIFICATION = '8 purchase';
	
	/** @var IEntityRepository */
	private $entityRepository;
	
	/** @var Nette\Database\Context */
	private $database;
	
	/** @var IPurchaseRepository */
	private $purchaseItemRepository;
	
	/** @var IPurchaseStatusRepository */
	private $purchaseStatusRepository;
	
	/** @var ICountryRepository */
	private $countryRepository;
	
	/** @var IProductRepository */
	private $productRepository;
	
	public function __construct(IEntityRepository $entityRepository, Nette\Database\Context $database, IPurchaseItemRepository $purchaseItemRepository, IPurchaseStatusRepository $purchaseStatusRepository, ICountryRepository $countryRepository, IProductRepository $productRepository){
		$this->entityRepository = $entityRepository;
		$this->database = $database;
		$this->purchaseItemRepository = $purchaseItemRepository;
		$this->purchaseStatusRepository = $purchaseStatusRepository;
		$this->countryRepository = $countryRepository;
		$this->productRepository = $productRepository;
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

	public function findById(int $id): Purchase
	{
		$queryResult = $this->database
			->query("
				SELECT *
				FROM purchase
				WHERE id = ?
			", $id)
			->fetch();

		if(!$queryResult){
			throw new \Exception('Purchase not found.');
		}

		return $purchase = $this->queryResultRowToObject($queryResult);
	}

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
	
	public function update($purchase): int
	{
		$purchaseArray = $purchase->toArray();
		$id = $purchaseArray['id'];
		unset($purchaseArray['id']);

		$this->database->beginTransaction();

		$howDidItGo = $this->database->query("
			UPDATE purchase
			SET ", $purchaseArray, "
			WHERE id = ?", $id
		);

		if($purchase->getPurchaseStatus()->getMeansCancelled()){
			$rowAffectedByIncreasingAmounts = $this->productRepository->increaseAvailableAmountsByCancelledPurchaseData($purchase->getPurchaseItems());
			if($rowAffectedByIncreasingAmounts !== count($purchase->getPurchaseItems())){
				$this->database->rollback();
				throw new \Exception('Unable to increase amounts of all products.');
			} else {
				$this->database->commit();
			}
		}

		return $howDidItGo->getRowCount();
	}

	public function delete($identification)
	{
		$purchase = $this->findById(intval($identification));

		$this->database->beginTransaction();

		$howDidItGo = $this->database->query("
			DELETE FROM
			purchase
			WHERE id = ?
		", $identification);

		if(!$purchase->getPurchaseStatus()->getMeansCancelled()){
			$rowAffectedByIncreasingAmounts = $this->productRepository->increaseAvailableAmountsByCancelledPurchaseData($purchase->getPurchaseItems());
			if($rowAffectedByIncreasingAmounts !== count($purchase->getPurchaseItems())){
				$this->database->rollback();
				throw new \Exception('Unable to increase amounts of all products.');
			}
		}

		$this->database->commit();

		return $howDidItGo->getRowCount();
	}

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
			$arrayOfPurchaseObjects[] = $this->queryResultRowToObject($row);
		}
		
		return $arrayOfPurchaseObjects;
	}
	
	private function queryResultRowToObject($queryResultRow): Purchase
	{
		$queryResultRow->customerCountry = $this->countryRepository->findById($queryResultRow->customerCountry);
		if(!is_null($queryResultRow->deliveryCountry)){
			$queryResultRow->deliveryCountry = $this->countryRepository->findById($queryResultRow->deliveryCountry);			
		}
		$queryResultRow->purchaseStatus = $this->purchaseStatusRepository->findById($queryResultRow->purchasestatus_id);
		$queryResultRow->purchaseItems = $this->purchaseItemRepository->findByPurchaseId($queryResultRow->id);
		
		return $purchase = PurchaseFactory::createFromObject($queryResultRow);
	}
}
