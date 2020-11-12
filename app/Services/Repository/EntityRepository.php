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
	
	/** @var Nette\Database\Context */
	private $database;
	
	/** @var EntityFactory */
	private $entityFactory;

	public function __construct(Nette\Database\Context $database, EntityFactory $entityFactory){
		$this->database = $database;
		$this->entityFactory = $entityFactory;
	}
	
	public function findAll(): Array
	{
		$queryResult = $this->database->query("
			SELECT *
			FROM entity
			ORDER BY name ASC
		");
		
		$arrayOfEntities = [];
		while($row = $queryResult->fetch()){
			$arrayOfEntities[] = $this->entityFactory->createFromObject($row);
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
		
		return $this->entityFactory->createFromObject($queryResult);
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
		
		return $this->entityFactory->createFromObject($queryResult);
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
