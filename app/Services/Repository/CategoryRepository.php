<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Nette;
use App\Services\Repository\BaseRepository;
use App\Entity\Category;
use App\Entity\Factory\CategoryFactory;
use App\Services\Repository\RepositoryInterface\IRepository;
use App\Services\Repository\RepositoryInterface\ICategoryRepository;


final class CategoryRepository extends BaseRepository implements IRepository, ICategoryRepository
{
	private $database;

	public function __construct(Nette\Database\Context $database){
		$this->database = $database;
	}
	
	public function findAll(): Array
	{
		$queryResult = $this->database
			->query("
				SELECT *
				FROM category		
			");
		
		$arrayOfCategories = [];		
		while($row = $queryResult->fetch()){
			$arrayOfCategories[] = CategoryFactory::createFromObject($row);
		}

		return $arrayOfCategories;
	}
	
	public function find(string $identification): Category
	{
		$identification = $this->chopIdentification($identification);
		
		$queryResult = $this->database
			->query("
				SELECT *
				FROM category
				WHERE id = ? AND name = ?
				", $identification['id'], $identification['name'])
			->fetch();
		
		//as the $identification variable should always be valid (one should always find a category with given id and name), I decided to throw exception in case nothing is found rather than return null
		if(is_null($queryResult)){ 
			throw new \Exception('Nothing found.');
		}

		return CategoryFactory::createFromObject($queryResult);
	}
	
	public function insert($category): int
	{
		$howDidItGo = $this->database->query("
			INSERT INTO category
			", $category->toArray());
			
		return $howDidItGo->getRowCount();
	}
	
	public function update($category)
	{
		$id = $category->getId();
		$categoryArray = $category->toArray();
		unset($categoryArray['id']);
		
		$howDidItGo = $this->database->query("
			UPDATE category 
			SET", $categoryArray, "
			WHERE id = ?", $id);
			
		return $howDidItGo->getRowCount();
	}
	
	public function delete(string $identification)
	{
		$identification = $this->chopIdentification($identification);
		
		$howDidItGo = $this->database->query("
			DELETE FROM category
			WHERE id = ? AND name = ?
		", $identification['id'], $identification['name']);
		
		return $howDidItGo->getRowCount();
	}
	
	public function getArrayOfUsedNames($currentCategoryId = null): Array
	{
		if(is_null($currentCategoryId)){
			$usedNames = $this->database
				->query("
					SELECT name
					FROM category
				")
				->fetchPairs();		
		} else {
			$usedNames = $this->database
				->query("
					SELECT name
					FROM category
					WHERE id != ?
				", $currentCategoryId)
				->fetchPairs();
		}
		
		for($i = 0; $i < count($usedNames); $i++){
			$usedNames[$i] = mb_strtolower($usedNames[$i]);
		}
		return $usedNames;
	}
}
