<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Nette;
use App\Entity\Delivery;
use App\Entity\Factory\DeliveryFactory;
use App\Services\Repository\BaseRepository;
use App\Services\Repository\RepositoryInterface\IRepository;
use App\Services\Repository\RepositoryInterface\ICreatableAndDeleteableEntityRepository;
use App\Services\Repository\RepositoryInterface\IDeliveryRepository;
use App\Services\Repository\RepositoryInterface\IEntityRepository;
use App\Services\Repository\RepositoryInterface\INameableEntityRepository;
use App\Services\Repository\RepositoryInterface\ISelectableEntityRepository;


final class DeliveryRepository extends BaseRepository implements ICreatableAndDeleteableEntityRepository, INameableEntityRepository, ISelectableEntityRepository, IDeliveryRepository
{
	private const ENTITY_IDENTIFICATION = '5 delivery';
	
	/** @var IEntityRepository */
	private $entityRepository;
	
	/** @var Nette\Database\Context */
	private $database;
	
	/** @var DeliveryFactory */
	private $deliveryFactory;

	public function __construct(IEntityRepository $entityRepository, Nette\Database\Context $database, DeliveryFactory $deliveryFactory){
		$this->entityRepository = $entityRepository;
		$this->database = $database;
		$this->deliveryFactory = $deliveryFactory;
	}
	
	public function findAll(): Array
	{
		$queryResult = $this->database
			->query("
				SELECT *
				FROM delivery
				ORDER BY name ASC
			");
		
		$arrayOfCountries = [];		
		while($row = $queryResult->fetch()){
			$arrayOfCountries[] = $this->deliveryFactory->createFromObject($row);
		}
		
		return $arrayOfCountries;
	}
	
	public function find(string $identification)
	{
		$identification = $this->chopIdentification($identification);
		
		$queryResult = $this->database
			->query("
				SELECT *
				FROM delivery
				WHERE id = ? AND name = ?
				", $identification['id'], $identification['name']
				)
			->fetch();
		
		if(is_null($queryResult)){
			throw new \Exception('No delivery found.');
		}
		
		return $delivery = $this->deliveryFactory->createFromObject($queryResult);
	}
	
	public function findById(int $id)
	{
		$queryResult = $this->database
			->query("
				SELECT *
				FROM delivery
				WHERE id = ?
				", $id)
			->fetch();

		if(is_null($queryResult)){
			throw new \Exception('No delivery found.');
		}
		
		return $delivery = $this->deliveryFactory->createFromObject($queryResult);
	}
	
	public function insert($delivery)
	{
		try{
			$delivery->setId($this->generateNewId($this->getUsedIds(), $this->entityRepository->find(self::ENTITY_IDENTIFICATION)));
		} catch(\Exception $ex){
			throw $ex;
		}
		
		$howDidItGo = $this->database->query("
			INSERT INTO delivery
			", $delivery->toArray());
			
		return $howDidItGo->getRowCount();
	}
	
	public function update($delivery)
	{
		$id = $delivery->getId();
		$deliveryArray = $delivery->toArray();
		unset($deliveryArray['id']);
		
		$howDidItGo = $this->database->query("
			UPDATE delivery
			SET", $deliveryArray, "
			WHERE id = ?", $id);
		
		return $howDidItGo->getRowCount();
	}
	
	public function delete(string $identification)
	{
		$identification = $this->chopIdentification($identification);
		
		$howDidItGo = $this->database->query("
			DELETE FROM delivery
			WHERE id = ? AND name = ?
		", $identification['id'], $identification['name']);
		
		return $howDidItGo->getRowCount();
	}

	private function getUsedIds(): array
	{
		$usedIds = $this->database
			->query("
				SELECT id
				FROM delivery
			")
			->fetchPairs();
		
		return $usedIds;
	}

	public function getArrayOfUsedNames($currentDeliveryId = null): Array
	{
		if(is_null($currentDeliveryId)){
			$usedNames = $this->database
				->query("
					SELECT name
					FROM delivery
				")
				->fetchPairs();		
		} else {
			$usedNames = $this->database
				->query("
					SELECT name
					FROM delivery
					WHERE id != ?
				", $currentDeliveryId)
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
				FROM delivery
			")
			->fetchPairs();
			
		return $queryResult;
	}
}

