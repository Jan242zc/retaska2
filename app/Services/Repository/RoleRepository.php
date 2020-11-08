<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Nette\Database\Context;
use App\Services\Repository\BaseRepository;
use App\Services\Repository\RepositoryInterface\IRoleRepository;
use App\Services\Repository\RepositoryInterface\IEntityRepository;
use App\Services\Repository\RepositoryInterface\ICreatableAndDeleteableEntityRepository;
use App\Services\Repository\RepositoryInterface\ISelectableEntityRepository;
use App\Services\Repository\RepositoryInterface\INameableEntityRepository;
use App\Entity\Role;
use App\Entity\Factory\RoleFactory;


final class RoleRepository extends BaseRepository implements ICreatableAndDeleteableEntityRepository, INameableEntityRepository, ISelectableEntityRepository, IRoleRepository
{
	private const ENTITY_IDENTIFICATION = '10 role';
	
	/** @var Context */
	private $database;
	
	/** @var RoleFactory */
	private $roleFactory;
	
	/** @var IEntityRepository */
	private $entityRepository;
	
	public function __construct(Context $database, RoleFactory $roleFactory, IEntityRepository $entityRepository){
		$this->database = $database;
		$this->roleFactory = $roleFactory;
		$this->entityRepository = $entityRepository;
	}
	
	public function findAll(): Array
	{
		$queryResult = $this->database->query("
			SELECT *
			FROM roles
		");
		
		return $this->queryResultsToObjects($queryResult);
	}
	
	public function findById(int $id): Role
	{
		
	}
	
	public function insert($role): int
	{
		try{
			$role->setId($this->generateNewId($this->getUsedIds(), $this->entityRepository->find(self::ENTITY_IDENTIFICATION)));
		} catch(\Exception $ex){
			throw $ex;
		}
		
		$howDidItGo = $this->database->query("
			INSERT INTO	roles
			", $role->toArray());

		return $howDidItGo->getRowCount();
	}
	
	public function update($role): int
	{
		
	}
	
	public function findAllForForm(): Array
	{
		
	}
	
	public function find($identification): Role
	{
		
	}
	
	public function getArrayOfUsedNames($currentRoleId = null): Array
	{
		if(is_null($currentRoleId)){
			$usedNames = $this->database
				->query("
					SELECT name
					FROM roles
				")
				->fetchPairs();		
		} else {
			$usedNames = $this->database
				->query("
					SELECT name
					FROM roles
					WHERE id != ?
				", $currentRoleId)
				->fetchPairs();
		}
		
		for($i = 0; $i < count($usedNames); $i++){
			$usedNames[$i] = mb_strtolower($usedNames[$i]);
		}
		return $usedNames;
	}
	
	public function delete($identification): int
	{
		
	}

	private function getUsedIds(): array
	{
		$usedIds = $this->database
			->query("
				SELECT id
				FROM roles
			")
			->fetchPairs();
		
		return $usedIds;
	}
	
	private function queryResultsToObjects($queryResults): Array
	{
		$roles = [];
		while($row = $queryResults->fetch()){
			$roles[] = $this->queryResultToObject($row);
		}
		
		return $roles;
	}
	
	private function queryResultToObject($queryResult): Role
	{
		return $this->roleFactory->createFromObject($queryResult);
	}
}
