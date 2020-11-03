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
	
	/** @var PurchaseFactory */
	private $purchaseFactory;
	
	public function __construct(IEntityRepository $entityRepository, Nette\Database\Context $database, IPurchaseItemRepository $purchaseItemRepository, IPurchaseStatusRepository $purchaseStatusRepository, ICountryRepository $countryRepository, IProductRepository $productRepository, PurchaseFactory $purchaseFactory){
		$this->entityRepository = $entityRepository;
		$this->database = $database;
		$this->purchaseItemRepository = $purchaseItemRepository;
		$this->purchaseStatusRepository = $purchaseStatusRepository;
		$this->countryRepository = $countryRepository;
		$this->productRepository = $productRepository;
		$this->purchaseFactory = $purchaseFactory;
	}
	
	public function findAll(): Array
	{
		$queryResult = $this->database->query("
			SELECT * 
			FROM purchase
			ORDER BY created_at DESC
		");

		try {
			$arrayOfPurchases = $this->queryResultToArrayOfObjects($queryResult);
		} catch(\Exception $ex){
			throw $ex;
		}

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

		try{
			return $purchase = $this->queryResultRowToObject($queryResult);
		} catch (\Exception $ex){
			throw $ex;
		}
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

		$this->database->beginTransaction();

		$howDidItGo = $this->database->query("
			INSERT INTO purchase
			", $purchase->toArray());

		$numberOfItems = count($purchase->getPurchaseItems());

		try{
			if($this->purchaseItemRepository->insertMultiple($purchase->getId(), $purchase->getPurchaseItems()) !== $numberOfItems){
				$this->database->rollback();
				throw new \Exception('Could not insert all items.');
			}
			if($this->productRepository->decreaseAvailableAmountsByProductQuantityArrays($purchase->itemsToProductIdQuantityArray()) !== $numberOfItems){
				$this->database->rollback();
				throw new \Exception('Could decrease all products amounts.');
			}
			$this->database->commit();
		} catch(\Exception $ex){
			$this->database->rollback();
			throw $ex;
		}

		return $howDidItGo->getRowCount();
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
			try{
				$this->increaseAvailableAmounts($purchase);
			} catch (\Exception $ex){
				$this->database->rollback();
				throw $ex;
			}
		}

		$this->database->commit();

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
			try{
				$this->increaseAvailableAmounts($purchase);
			} catch (\Exception $ex){
				$this->database->rollback();
				throw $ex;
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
			try{
				$arrayOfPurchaseObjects[] = $this->queryResultRowToObject($row);
			} catch (\Exception $ex){
				throw $ex;
			}
		}
		
		return $arrayOfPurchaseObjects;
	}
	
	private function queryResultRowToObject($queryResultRow): Purchase
	{
		try{
			return $purchase = $this->purchaseFactory->createFromObject($queryResultRow);			
		} catch (\Exception $ex){
			throw $ex;
		}
	}

	private function increaseAvailableAmounts($purchase): void
	{
		$rowAffectedByIncreasingAmounts = $this->productRepository->increaseAvailableAmountsByProductQuantityArrays($purchase->itemsToProductIdQuantityArray());
		if($rowAffectedByIncreasingAmounts !== count($purchase->getPurchaseItems())){
			throw new \Exception('Unable to increase amounts of all products.');
		}
	}
}
