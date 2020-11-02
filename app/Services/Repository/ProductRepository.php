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
use App\Services\Repository\RepositoryInterface\INameableEntityRepository;
use App\Entity\Product;
use App\Entity\BasketItem;
use App\Entity\PurchaseItem;
use App\Entity\Factory\ProductFactory;
use App\Entity\Factory\CategoryFactory;


class ProductRepository extends BaseRepository implements ICreatableAndDeleteableEntityRepository, INameableEntityRepository, IProductRepository
{
	private const ENTITY_IDENTIFICATION = '2 product';
	private $database;
	private $productFactory;
	
	public function __construct(Nette\Database\Context $database, IEntityRepository $entityRepository, ICategoryRepository $categoryRepository, ProductFactory $productFactory){
		$this->database = $database;
		$this->entityRepository = $entityRepository;
		$this->categoryRepository = $categoryRepository;
		$this->productFactory = $productFactory;
	}
	
	public function findAll(int $limit = null, int $offset = 0): Array
	{
		$limit = $limit ?? $this->getProductsCount(); //I'd much rather use ALL, but that can't be used without string concat (which is an evil thing :P)

		$queryResult = $this->database->query("
			SELECT *
			FROM product
			LIMIT ?
			OFFSET ?
		", $limit, $offset);
		
		try{
			$arrayOfProducts = $this->queryResultToArrayOfObjects($queryResult);
		} catch (\Exception $ex){
			throw $ex;
		}
		
		return $arrayOfProducts;
	}
	
	public function findByCategory($category, int $limit = null, int $offset = 0): Array
	{
		$limit = $limit ?? $this->getProductsCountByCategory($category); //I'd much rather use ALL, but that can't be used without string concat (which is an evil thing :P)
		
		$queryResult = $this->database->query("
			SELECT *
			FROM product
			WHERE category = ?
			LIMIT ?
			OFFSET ?
		", $category, $limit, $offset);
		
		try{
			$arrayOfProducts = $this->queryResultToArrayOfObjects($queryResult);
		} catch (\Exception $ex){
			throw $ex;
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
		return $product = $this->productFactory->createFromObject($queryResult);
		} catch (\Exception $ex){
			throw $ex;
		}
		
	}
	
	public function findById(int $id): Product
	{
		$queryResult = $this->database
			->query("
				SELECT *
				FROM product
				WHERE id = ?
				", $id
				)
			->fetch();
		
		if(is_null($queryResult)){
			throw new \Exception('No product found.');
		}
		
		try{
			return $product = $this->productFactory->createFromObject($row);	
		} catch (\Exception $ex){
			throw $ex;
		}
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
	
	public function getProductsCount(): int
	{
		return $this->database
			->query("
					SELECT COUNT(*)
					FROM product
				")
			->fetchField();
	}
	
	public function getProductsCountByCategory($category): int
	{
		return $this->database
			->query("
					SELECT COUNT(*)
					FROM product
					WHERE category = ?
				", $category)
			->fetchField();
	}
	
	public function findTopProducts($limit): Array
	{
		$queryResult = $this->database->query("
			SELECT *
			FROM product
			ORDER BY price DESC
			LIMIT ?
		", $limit);
		
		return $topProducts = $this->queryResultToArrayOfObjects($queryResult);
	}

	public function decreaseAvailableAmountsByProductQuantityArrays(Array $productQuantityArrays): int
	{
		$rows = 0;
		foreach($productQuantityArrays as $productQuantityArray){
			$rows += $this->decreaseAvailableAmountByProductQuantityArray($productQuantityArray);
		}

		return $rows;
	}
	
	private function decreaseAvailableAmountByProductQuantityArray(Array $productQuantityArray): int
	{
		$howDidItGo = $this->database->query("
			UPDATE product
			SET amountAvailable = amountAvailable - ?
			WHERE id = ?
		", $productQuantityArray['quantity'], $productQuantityArray['product_id']);

		return $howDidItGo->getRowCount();
	}

	public function increaseAvailableAmountsByProductQuantityArrays(Array $productQuantityArrays): int
	{
		$rows = 0;

		foreach($productQuantityArrays as $productQuantityArray){
			$rows += $this->increaseAvailableAmountByProductQuantityArray($productQuantityArray);
		}

		return $rows;
	}

	private function increaseAvailableAmountByProductQuantityArray(Array $productQuantityArray): int
	{
		$howDidItGo = $this->database->query("
			UPDATE product
			SET amountAvailable = amountAvailable + ?
			WHERE id = ?
		", $productQuantityArray['quantity'], $productQuantityArray['product_id']);

		return $howDidItGo->getRowCount();
	}

	private function queryResultToArrayOfObjects($queryResult): Array
	{
		$arrayOfProducts = [];
		while($row = $queryResult->fetch()){
			//I chose this over JOIN as no extension of the category entity will require no subsequent changes of this method
			try{
				$arrayOfProducts[] = $this->productFactory->createFromObject($row);
			} catch (\Exception $ex){
				throw $ex;
			}
		}
		
		return $arrayOfProducts;
	}
}
