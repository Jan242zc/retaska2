<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Nette;
use App\Services\Repository\RepositoryInterface\IRepository;


abstract class BaseRepository implements IRepository
{
	protected function chopIdentification(string $string): Array
    {
        for($i = 0; $i < strlen($string); ++$i){
			if($string[$i] === ' ')
				break;
		}
        
		return $result = [
			'id' => substr($string, 0, $i),
			'name' => trim(substr($string, $i + 1))
		];
    }
	
	protected function generateNewId($usedIds, $entity): int 
	{
		$idLimit = $entity->getIdLimit();
		if(count($usedIds) >= $idLimit){
			throw new \Exception('More items that possible ids.');
		}
        while(in_array($id = rand(0, $idLimit), $usedIds)){
        }
		return $id;
	}
}
