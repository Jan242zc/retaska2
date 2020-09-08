<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Nette;
use App\Services\Repository\BaseRepository;
use App\Services\Repository\RepositoryInterface\IRepository;
use App\Services\Repository\RepositoryInterface\ICreatableAndDeleteableEntityRepository;
use App\Services\Repository\RepositoryInterface\IProductRepository;
use App\Services\Repository\RepositoryInterface\IEntityRepository;
use App\Services\Repository\RepositoryInterface\ICategoryRepository;
use App\Entity\Product;
use App\Entity\Factory\ProductFactory;
use App\Entity\Factory\CategoryFactory;


class ProductRepository extends BaseRepository implements ICreatableAndDeleteableEntityRepository, IProductRepository
{
	private const ENTITY_IDENTIFICATION = '2 product';
	private $database;
	
	public function __construct(Nette\Database\Context $database, IEntityRepository $entityRepository, ICategoryRepository $categoryRepository){
		$this->database = $database;
		$this->entityRepository = $entityRepository;
		$this->categoryRepository = $categoryRepository;
	}
	
	public function findAll(): Array
	{
		$queryResult = $this->database->query("
			SELECT *
			FROM product
		");

		$arrayOfProducts = [];
		while($row = $queryResult->fetch()){
			//I chose this over JOIN as no extension of the category entity will require no subsequent changes of this method
			$row->category = $this->categoryRepository->findById($row->category); 
			$arrayOfProducts[] = ProductFactory::createFromObject($row);
		}
		
		return $arrayOfProducts;
	}
	
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
