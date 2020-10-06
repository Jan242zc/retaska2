<?php

declare(strict_types=1);

namespace App\Services\Repository\RepositoryInterface;


interface INameableEntityRepository //aka repository of an entity which has a name field
{
	public function find(string $identification);
	public function getArrayOfUsedNames($currentProductId = null): Array;
}
