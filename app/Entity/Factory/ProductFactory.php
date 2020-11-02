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

		$data['category'] = $this->findCategoryForProduct($data['category']);

		return new Product($id, $data['name'], $data['price'], $data['category'], $data['material'], $data['amountAvailable'], $data['description']);
	}
	
	public function createFromObject($object): Product
	{
		if(!$id = $object->id){
			$id = null;
		}

		$object->category = $this->findCategoryForProduct($object->category);

		return new Product($id, $object->name, $object->price, $object->category, $object->material, $object->amountAvailable, $object->description);
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
