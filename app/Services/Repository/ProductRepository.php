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
			try{
				$row->category = $this->categoryRepository->findById($row->category); 				
			} catch (\Exception $ex){
				throw $ex;
			}
			
			$arrayOfProducts[] = ProductFactory::createFromObject($row);
		}
		
		return $arrayOfProducts;
	}
	
	public function find(string $identification): Product
	{
		$identification = $this->chopIdentification($identification);
		
		$queryResult = $this->database
			->query("
				SELECT *
				FROM product
				WHERE id = ? AND name = ?
				", $identification['id'], $identification['name']
				)
			->fetch();
		
		if(is_null($queryResult)){
			throw new \Exception('No product found.');
		}
		
		try{
			$queryResult->category = $this->categoryRepository->findById($queryResult->category);			
		} catch (\Exception $ex){
			throw $ex;
		}
		
		return $product = ProductFactory::createFromObject($queryResult);
	}
	
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
	{
		$id = $product->getId();
		$productArray = $product->toArray();
		unset($productArray['id']);
		
		$howDidItGo = $this->database->query("
			UPDATE product
			SET", $productArray, "
			WHERE id = ?", $id);
		
		return $howDidItGo->getRowCount();
	}
	
	public function delete(string $identification): int
	{
		$identification = $this->chopIdentification($identification);
		
		$howDidItGo = $this->database->query("
			DELETE FROM product
			WHERE id = ? AND name = ?
		", $identification['id'], $identification['name']);
		
		return $howDidItGo->getRowCount();
	}
	
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
	
	public function getArrayOfUsedNames($currentProductId = null): Array
	{
		if(is_null($currentProductId)){
			$usedNames = $this->database
				->query("
					SELECT name
					FROM product
				")
				->fetchPairs();		
		} else {
			$usedNames = $this->database
				->query("
					SELECT name
					FROM product
					WHERE id != ?
				", $currentProductId)
				->fetchPairs();
		}
		
		for($i = 0; $i < count($usedNames); $i++){
			$usedNames[$i] = mb_strtolower($usedNames[$i]);
		}
		return $usedNames;
	}
}
