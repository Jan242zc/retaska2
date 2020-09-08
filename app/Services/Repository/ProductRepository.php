<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Nette;
use App\Services\Repository\BaseRepository;
use App\Services\Repository\RepositoryInterface\IRepository;
use App\Services\Repository\RepositoryInterface\ICreatableAndDeleteableEntityRepository;
use App\Services\Repository\RepositoryInterface\IProductRepository;
use App\Services\Repository\RepositoryInterface\IEntityRepository;
use App\Entity\Product;
use App\Entity\Factory\ProductFactory;


class ProductRepository extends BaseRepository implements ICreatableAndDeleteableEntityRepository, IProductRepository
{
	private const ENTITY_IDENTIFICATION = '2 product';
	private $database;
	
	public function __construct(Nette\Database\Context $database, IEntityRepository $entityRepository){
		$this->database = $database;
		$this->entityRepository = $entityRepository;
	}
	
	public function findAll(): Array
	{}
	
	public function find(string $identification): Product
	{}
	
	public function insert($product): int
	{
		try{
			$product->setId($this->generateNewId($this->getUsedIds(), $this->entityRepository->find(self::ENTITY_IDENTIFICATION)));
		} catch(\Exception $ex){
			throw $ex;
		}
		
		$howDidItGo = $this->database->query("
			INSERT INTO product
			", $product->toArray());
			
		return $howDidItGo->getRowCount();
	}
	
	public function update($product): int
	{}
	
	public function delete(string $identification): int
	{}
	
	private function getUsedIds(): array
	{
		$usedIds = $this->database
			->query("
				SELECT id
				FROM product
			")
			->fetchPairs();
		
		return $usedIds;
	}
}
