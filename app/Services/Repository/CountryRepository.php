<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Nette;
use App\Entity\Country;
use App\Entity\Factory\CountryFactory;
use App\Services\Repository\BaseRepository;
use App\Services\Repository\RepositoryInterface\ICreatableAndDeleteableEntityRepository;
use App\Services\Repository\RepositoryInterface\ICountryRepository;
use App\Services\Repository\RepositoryInterface\IEntityRepository;
use App\Services\Repository\RepositoryInterface\INameableEntityRepository;
use App\Services\Repository\RepositoryInterface\ISelectableEntityRepository;


final class CountryRepository extends BaseRepository implements ICreatableAndDeleteableEntityRepository, INameableEntityRepository, ISelectableEntityRepository,ICountryRepository
{
	private const ENTITY_IDENTIFICATION = '3 country';
	
	/** @var IEntityRepository */
	private $entityRepository;
	
	/** @var Nette\Database\Context */
	private $database;
	
	/** @var CountryFactory */
	private $countryFactory;

	public function __construct(IEntityRepository $entityRepository, Nette\Database\Context $database, CountryFactory $countryFactory){
		$this->entityRepository = $entityRepository;
		$this->database = $database;
		$this->countryFactory = $countryFactory;
	}
	
	public function findAll(): Array
	{
		$queryResult = $this->database
			->query("
				SELECT *
				FROM country
				ORDER BY name ASC
			");
		
		$arrayOfCountries = [];		
		while($row = $queryResult->fetch()){
			$arrayOfCountries[] = $this->countryFactory->createFromObject($row);
		}
		
		return $arrayOfCountries;
	}
	
	public function find(string $identification)
	{
		$identification = $this->chopIdentification($identification);
		
		$queryResult = $this->database
			->query("
				SELECT *
				FROM country
				WHERE id = ? AND name = ?
				", $identification['id'], $identification['name']
				)
			->fetch();

		if(is_null($queryResult)){
			throw new \Exception('No country found.');
		}
		
		return $country = $this->countryFactory->createFromObject($queryResult);
	}
	
	public function findById(int $id)
	{
		$queryResult = $this->database
			->query("
				SELECT *
				FROM country
				WHERE id = ?
				", $id)
			->fetch();

		if(is_null($queryResult)){
			throw new \Exception('No country found.');
		}
		
		return $country = $this->countryFactory->createFromObject($queryResult);
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
	
	public function update($country)
	{
		$id = $country->getId();
		$countryArray = $country->toArray();
		unset($countryArray['id']);
		
		$howDidItGo = $this->database->query("
			UPDATE country
			SET", $countryArray, "
			WHERE id = ?", $id);
		
		return $howDidItGo->getRowCount();
	}
	
	public function delete(string $identification)
	{
		$identification = $this->chopIdentification($identification);
		
		try{
			$howDidItGo = $this->database->query("
				DELETE FROM country
				WHERE id = ? AND name = ?
			", $identification['id'], $identification['name']);
		} catch (\Exception $ex){
			throw $ex;
		}

		return $howDidItGo->getRowCount();
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

	public function getArrayOfUsedNames($currentCountryId = null): Array
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
	
	public function findAllForForm(): Array
	{
		$queryResult = $this->database
			->query("
				SELECT *
				FROM country
			")
			->fetchPairs();
			
		return $queryResult;
	}
}

