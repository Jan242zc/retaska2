<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Nette;
use App\Services\Repository\BaseRepository;
use App\Entity\Entity;
use App\Entity\Factory\EntityFactory;
use App\Services\Repository\RepositoryInterface\IRepository;
use App\Services\Repository\RepositoryInterface\IEntityRepository;


final class EntityRepository extends BaseRepository implements IEntityRepository
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
		$identification = $this->chopIdentification($identification);
		
		$queryResult = $this->database
			->query("
				SELECT *
				FROM entity
				WHERE id = ? AND name = ?
			", $identification['id'], $identification['name'])
			->fetch();
			
		if(is_null($queryResult)){
			throw new \Exception('Entity not found');
		}
		
		return EntityFactory::createFromObject($queryResult);
	}
	
	public function findById(int $id)
	{
		$queryResult = $this->database
			->query("
				SELECT *
				FROM entity
				WHERE id = ?
			", $id)
			->fetch();
			
		if(is_null($queryResult)){
			throw new \Exception('Entity not found');
		}
		
		return EntityFactory::createFromObject($queryResult);
	}

	public function update($entity)
	{
		$id = $entity->getId();
		$entityArray = $entity->toArray();
		unset($entityArray['id']);
		
		$howDidItGo = $this->database->query("
			UPDATE entity
			SET idLimit = ?
			WHERE id = ?
		", $entity->getIdLimit(), $id);
		
		return $howDidItGo->getRowCount();
	}
}
