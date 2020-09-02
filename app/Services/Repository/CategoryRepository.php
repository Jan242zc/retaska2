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
		
	}
	
	public function find(string $identification)
	{
		
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
		
	}
	public function delete($category)
	{
		
	}
}
