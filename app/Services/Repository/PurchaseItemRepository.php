<?php

declare(strict_types=1);

namespace App\Repository;

use Nette;
use App\Services\Repository\RepositoryInterface\IPurchaseItemRepository;
use App\Services\Repository\RepositoryInterface\ICreatableAndDeleteableRepository;
use App\Services\Repository\EntityRepository;
use App\Services\Repository\BaseRepository;


final class PurchaseItemRepository extends BaseRepository implements ICreatableAndDeleteableRepository, IPurchaseItemRepository
{
	private const ENTITY_IDENTIFICATION = '9 purchaseitem';
	private $entityRepository;
	private $database;
	
	public function __construct(IEntityRepository $entityRepository, Nette\Database\Context $database){
		$this->entityRepository = $entityRepository;
		$this->database = $database;
	}
	
	public function findAll(): Array;
	public function findById(int $id);
	public function update($object);
	public function insert($object);
	public function delete(string $identification);
}
