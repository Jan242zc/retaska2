<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Nette;
use App\Services\Repository\BaseRepository;
use App\Services\Repository\RepositoryInterface\IRepository;
use App\Services\Repository\RepositoryInterface\ICreatableAndDeleteableEntityRepository;
use App\Services\Repository\RepositoryInterface\IProductRepository;
use App\Entity\Product;
use App\Entity\Factory\ProductFactory;


class ProductRepository extends BaseRepository implements ICreatableAndDeleteableEntityRepository, IProductRepository
{
	private $database;
	
	public function __construct(Nette\Database\Context $database){
		$this->database = $database;
	}
	
	public function findAll(): Array
	{}
	
	public function find(string $identification): Product
	{}
	
	public function insert($product): int
	{}
	
	public function update($product): int
	{}
	
	public function delete(string $identification): int
	{}
}
