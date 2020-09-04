<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Nette;
use App\Services\Repository\BaseRepository;
use App\Entity\Entity;
use App\Entity\Factory\EntityFactory;
use App\Services\Repository\RepositoryInterface\IRepository;
use App\Services\Repository\RepositoryInterface\IEntityRepository;


final class EntityRepository extends BaseRepository implements IRepository, IEntityRepository
{
	private $database;

	public function __construct(Nette\Database\Context $database){
		$this->database = $database;
	}
	
	public function findAll(): Array
	{
		$queryResult = $this->database->query("
			SELECT *
			FROM entity
		");
		
		$arrayOfEntities = [];
		while($row = $queryResult->fetch()){
			$arrayOfEntities[] = EntityFactory::createFromObject($row);
		}
		
		return $arrayOfEntities;
	}
	
	public function find(string $identification)
	{
		
	}
	
	public function insert($entity)
	{
		
	}
	
	public function update($entity)
	{
		
	}
	
	public function delete(string $identification)
	{
		
	}
	
}
