<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Nette;
use App\Entity\Factory\CategoryFactory;
use App\Services\Repository\BaseRepository;
use App\Services\Repository\RepositoryInterface\IRepository;
use App\Services\Repository\RepositoryInterface\ICreatableAndDeleteableEntityRepository;
use App\Services\Repository\RepositoryInterface\ICountryRepository;
use App\Services\Repository\RepositoryInterface\IEntityRepository;

final class CountryRepository extends BaseRepository implements ICreatableAndDeleteableEntityRepository, ICountryRepository
{
	private const ENTITY_IDENTIFICATION = '3 country';
	private $entityRepository;
	private $database;

	public function __construct(IEntityRepository $entityRepository, Nette\Database\Context $database){
		$this->entityRepository = $entityRepository;
		$this->database = $database;
	}
	
	public function findAll(): Array
	{
		$queryResult = $this->database
			->query("
				SELECT *
				FROM country
			");
		
		$arrayOfCountries = [];		
		while($row = $queryResult->fetch()){
			$arrayOfCountries[] = CategoryFactory::createFromObject($row);
		}
		
		return $arrayOfCountries;
	}
	
	public function find(string $identification)
	{
		
	}
	
	public function insert($country)
	{
		try{
			$country->setId($this->generateNewId($this->getUsedIds(), $this->entityRepository->find(self::ENTITY_IDENTIFICATION)));
		} catch(\Exception $ex){
			throw $ex;
		}
		
		$howDidItGo = $this->database->query("
			INSERT INTO country
			", $country->toArray());
			
		return $howDidItGo->getRowCount();
	}
	
	public function update($object)
	{
		
	}
	
	public function delete(string $identification)
	{
		
	}

	private function getUsedIds(): array
	{
		$usedIds = $this->database
			->query("
				SELECT id
				FROM country
			")
			->fetchPairs();
		
		return $usedIds;
	}

	public function getArrayOfUsedNames($currentCountryId): Array
	{
		if(is_null($currentCountryId)){
			$usedNames = $this->database
				->query("
					SELECT name
					FROM country
				")
				->fetchPairs();		
		} else {
			$usedNames = $this->database
				->query("
					SELECT name
					FROM country
					WHERE id != ?
				", $currentCountryId)
				->fetchPairs();
		}
		
		for($i = 0; $i < count($usedNames); $i++){
			$usedNames[$i] = mb_strtolower($usedNames[$i]);
		}
		return $usedNames;
	}
}

