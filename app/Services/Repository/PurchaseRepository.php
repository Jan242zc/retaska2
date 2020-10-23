<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Nette;
use App\Services\Repository\BaseRepository;
use App\Services\Repository\RepositoryInterface\IPurchaseRepository;
use App\Services\Repository\RepositoryInterface\IPurchaseItemRepository;
use App\Services\Repository\RepositoryInterface\IEntityRepository;
use App\Services\Repository\RepositoryInterface\ICreatableAndDeleteableEntityRepository;
use App\Entity\Purchase;
use App\Entity\Factory\PurchaseItemFactory;


final class PurchaseRepository extends BaseRepository implements ICreatableAndDeleteableEntityRepository, IPurchaseRepository
{
	private const ENTITY_IDENTIFICATION = '8 purchase';
	private $entityRepository;
	private $database;
	private $purchaseItemRepository;
	
	public function __construct(IEntityRepository $entityRepository, Nette\Database\Context $database, IPurchaseItemRepository $purchaseItemRepository){
		$this->entityRepository = $entityRepository;
		$this->database = $database;
		$this->purchaseItemRepository = $purchaseItemRepository;
	}
	
	public function findAll(): Array
	{}
	public function findById(int $id)
	{}

	public function insert($purchase)
	{
		try{
			$purchase->setId($this->generateNewId($this->getUsedIds(), $this->entityRepository->find(self::ENTITY_IDENTIFICATION)));
		} catch(\Exception $ex){
			throw $ex;
		}

		$purchase = $purchase->toArray();
		$purchase['purchaseStatus_id'] = 1;

		$howDidItGo = $this->database->query("
			INSERT INTO purchase
			", $purchase);

		return [
			'id' => $purchase['id'],
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
}
