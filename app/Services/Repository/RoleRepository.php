<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Nette\Database\Context;
use App\Services\Repository\RepositoryInterface\IRoleRepository;
use App\Services\Repository\RepositoryInterface\ICreatableAndDeleteableEntityRepository;
use App\Services\Repository\RepositoryInterface\ISelectableEntityRepository;
use App\Services\Repository\RepositoryInterface\INameableEntityRepository;
use App\Entity\Role;
use App\Entity\Factory\RoleFactory;


final class RoleRepository implements ICreatableAndDeleteableEntityRepository, INameableEntityRepository, ISelectableEntityRepository, IRoleRepository
{
	/** @var Context */
	private $database;
	
	/** @var RoleFactory */
	private $roleFactory;
	
	public function __construct(Context $database, RoleFactory $roleFactory){
		$this->database = $database;
		$this->roleFactory = $roleFactory;
	}
	
	public function findAll(): Array
	{
		$queryResult = $this->database->query("
			SELECT *
			FROM roles
		");
		
		return $this->queryResultsToObjects($queryResult);
	}
	
	public function findById(): Role
	{
		
	}
	
	public function insert($role): int
	{
		$howDidItGo = $this->database->query("
			INSERT INTO
			roles",
			$role->toArray());

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
		
	}
	
	public function delete($identification): int
	{
		
	}
	
	private function queryResultsToObjects(array $queryResults): Array
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
