<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Nette;
use App\Services\Repository\RepositoryInterface\IPurchaseItemRepository;
use App\Services\Repository\RepositoryInterface\ICreatableAndDeleteableEntityRepository;
use App\Services\Repository\RepositoryInterface\IEntityRepository;
use App\Services\Repository\BaseRepository;
use App\Entity\Factory\PurchaseItemFactory;


final class PurchaseItemRepository extends BaseRepository implements ICreatableAndDeleteableEntityRepository, IPurchaseItemRepository
{
	private const ENTITY_IDENTIFICATION = '9 purchaseitem';
	
	/** @var IEntityRepository */
	private $entityRepository;
	
	/** @var Nette\Database\Context */
	private $database;
	
	/** @var PurchaseItemFactory */
	private $purchaseItemFactory;
	
	public function __construct(IEntityRepository $entityRepository, Nette\Database\Context $database, PurchaseItemFactory $purchaseItemFactory){
		$this->entityRepository = $entityRepository;
		$this->database = $database;
		$this->purchaseItemFactory = $purchaseItemFactory;
	}
	
	public function findAll(): Array
	{}
	
	public function findByPurchaseId(int $purchaseId): Array
	{
		$queryResult = $this->database->query("
			SELECT *
			FROM purchaseItem
			WHERE purchase_id = ?
		", $purchaseId);
		
		return $arrayOfPurchaseItems = $this->queryResultToArrayOfObjects($queryResult);
	}

	public function findById(int $id)
	{}
	public function update($object)
	{}
	
	public function insertMultiple(int $purchaseId, array $items): int
	{
		$totalRows = 0;
		
		foreach($items as $item){
			$item->setPurchaseId($purchaseId);
			try{
				$totalRows += $this->insert($item);				
			} catch(\Exception $ex){
				throw $ex;
			}
		}
		
		return $totalRows;
	}
	
	public function insert($purchaseItem)
	{
		try{
			$purchaseItem->setId($this->generateNewId($this->getUsedIds(), $this->entityRepository->find(self::ENTITY_IDENTIFICATION)));
		} catch(\Exception $ex){
			throw $ex;
		}

		$howDidItGo = $this->database->query("
			INSERT INTO purchaseitem
			", $purchaseItem->toArray());

		return $howDidItGo->getRowCount();
	}
	
	public function delete(string $identification)
	{}
	
	public function findXMostSoldInTheLastXDays(int $limit, int $days): Array
	{
		$queryResult = $this->database
			->query("
				SELECT pi.product_id, pi.product_name, SUM(pi.quantity) AS quantity
				FROM purchaseitem pi
				JOIN purchase p
					ON pi.purchase_id = p.id
				WHERE created_at > DATE_SUB(CURRENT_DATE(), INTERVAL ? DAY)
				GROUP BY pi.product_name
				ORDER BY SUM(quantity) DESC
				LIMIT ?
			", $days, $limit)
			->fetchAll();

		return $queryResult;
	}
	
	private function getUsedIds(): array
	{
		$usedIds = $this->database
			->query("
				SELECT id
				FROM purchaseitem
			")
			->fetchPairs();
		
		return $usedIds;
	}
	
	private function queryResultToArrayOfObjects($queryResult): Array
	{
		$arrayOfObjects = [];
		
		while($row = $queryResult->fetch()){
			$arrayOfObjects[] = $this->purchaseItemFactory->createFromObject($row);
		}
		
		return $arrayOfObjects;
	}
}
