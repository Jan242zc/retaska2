<?php

declare(strict_types=1);

namespace App\Services\Repository;

use Nette;


abstract class BaseRepository
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
}
