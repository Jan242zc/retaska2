<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Nette\Database\Context;
use App\Entity\UserData;
use App\Entity\Factory\UserDataFactory;
use App\Services\Repository\RepositoryInterface\IUserDataRepository;
use App\Services\Repository\RepositoryInterface\IEntityRepository;
use App\Services\Repository\RepositoryInterface\ICreatableAndDeleteableEntityRepository;
use App\Services\Repository\RepositoryInterface\INameableEntityRepository;
use App\Services\Repository\BaseRepository;
use Nette\Security\Passwords;


final class UserDataRepository extends BaseRepository implements ICreatableAndDeleteableEntityRepository, INameableEntityRepository, IUserDataRepository
{
	private const ENTITY_IDENTIFICATION = '11 userdata';
	
	/** @var Context */
	private $database;
	
	/** @var UserDataFactory */
	private $userDataFactory;
	
	/** @var IEntityRepository */
	private $entityRepository;
	
	/** @var Passwords */
	private $passwords;
	
	public function __construct(Context $database, UserDataFactory $userDataFactory, IEntityRepository $entityRepository, Passwords $passwords){
		$this->database = $database;
		$this->userDataFactory = $userDataFactory;
		$this->entityRepository = $entityRepository;
		$this->passwords = $passwords;
	}

	public function findAll(): Array
	{
		$queryResult = $this->database->query("
			SELECT *
			FROM userData
		");
		
		return $this->queryResultsToObjects($queryResult);
	}
	
	public function find($identification): UserData
	{
		$identification = $this->chopIdentification($identification);
		
		$queryResult = $this->database
			->query("
				SELECT *
				FROM userdata
				WHERE id = ? AND name = ?
				", $identification['id'], $identification['name']
				)
			->fetch();
		
		if(is_null($queryResult)){
			throw new \Exception('No userdata found.');
		}
		
		return $userData = $this->userDataFactory->createFromObject($queryResult);
	}
	
	public function findById(int $id): UserData
	{
		$queryResult = $this->database
			->query("
				SELECT *
				FROM userData
				WHERE id = ?
				", $id)
			->fetch();

		if(!is_null($queryResult)){
			return $userData = $this->userDataFactory->createFromObject($queryResult);
		}
		
		return $queryResult;
	}
	
	public function findByName(string $name): UserData
	{
		$queryResult = $this->database
			->query("
				SELECT *
				FROM userdata
				WHERE name = ?
			", $name)
			->fetch();

		if(is_null($queryResult)){
			throw new \Exception('UserData not found.');
		}

		return $this->queryResultToObject($queryResult);
	}
	
	public function insert($userData): int
	{
		try{
			$userData->setId($this->generateNewId($this->getUsedIds(), $this->entityRepository->find(self::ENTITY_IDENTIFICATION)));
		} catch(\Exception $ex){
			throw $ex;
		}

		$userData->setPassword($this->passwords->hash($userData->getPassword()));

		$howDidItGo = $this->database->query("
			INSERT INTO	userdata
			", $userData->toArray());

		return $howDidItGo->getRowCount();
	}
	
	public function update($userData): int
	{
		$id = $userData->getId();
		$userDataArray = $userData->toArray();
		unset($userDataArray['id']);
		
		$howDidItGo = $this->database->query("
			UPDATE userdata
			SET", $userDataArray, "
			WHERE id = ?", $id);
		
		return $howDidItGo->getRowCount();
	}

	public function getArrayOfUsedNames($currentUserDataId = null): Array
	{
		if(is_null($currentUserDataId)){
			$usedNames = $this->database
				->query("
					SELECT name
					FROM userdata
				")
				->fetchPairs();		
		} else {
			$usedNames = $this->database
				->query("
					SELECT name
					FROM userdata
					WHERE id != ?
				", $currentUserDataId)
				->fetchPairs();
		}
		
		for($i = 0; $i < count($usedNames); $i++){
			$usedNames[$i] = mb_strtolower($usedNames[$i]);
		}
		return $usedNames;
	}
	
	public function delete($identification): int
	{
		$identification = $this->chopIdentification($identification);
		
		$howDidItGo = $this->database->query("
			DELETE FROM userdata
			WHERE id = ? AND name = ?
		", $identification['id'], $identification['name']);
		
		return $howDidItGo->getRowCount();
	}

	private function getUsedIds(): array
	{
		$usedIds = $this->database
			->query("
				SELECT id
				FROM userdata
			")
			->fetchPairs();
		
		return $usedIds;
	}
	
	private function queryResultsToObjects($queryResults): Array
	{
		$userdata = [];
		while($row = $queryResults->fetch()){
			$userdata[] = $this->queryResultToObject($row);
		}
		
		return $userdata;
	}
	
	private function queryResultToObject($queryResult): UserData
	{
		return $this->userDataFactory->createFromObject($queryResult);
	}	
}
