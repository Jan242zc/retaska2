<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity;
use App\Entity\Category;
use App\Entity\Product;
use App\Services\Repository\RepositoryInterface\ICategoryRepository;


final class ProductFactory
{
	/** @var ICategoryRepository */
	private $categoryRepository;

	public function __construct(ICategoryRepository $categoryRepository){
		$this->categoryRepository = $categoryRepository;
	}
	
	public function createFromArray(array $data): Product
	{
		if(!$id = $data['id']){
			$id = null;
		}

		if(!isset($data['amountAvailable']) || is_null($data['amountAvailable'])){
			$amountAvailable = null;
		} else {
			$amountAvailable = $data['amountAvailable'];
		}

		$data['category'] = $this->findCategoryForProduct($data['category']);

		return new Product($id, $data['name'], $data['price'], $data['category'], $data['material'], $data['description'], $amountAvailable);
	}
	
	public function createFromObject($object): Product
	{
		if(!$id = $object->id){
			$id = null;
		}

		if(!isset($object->amountAvailable) || is_null($object->amountAvailable)){
			$amountAvailable = null;
		} else {
			$amountAvailable = $object->amountAvailable;
		}

		$object->category = $this->findCategoryForProduct($object->category);

		return new Product($id, $object->name, $object->price, $object->category, $object->material, $object->description, $amountAvailable);
		return new Product($id, $object->name, $object->price, $object->category, $object->material, $object->description, $amountAvailable);
	}
	
	private function findCategoryForProduct($categoryId): Category
	{
		try{
			return $this->categoryRepository->findById(intval($categoryId));			
		} catch (\Exception $ex) {
			throw $ex;
		}
	}
}
