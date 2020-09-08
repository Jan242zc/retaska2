<?php

declare(strict_types=1);

namespace App\Services\Repository\RepositoryInterface;


interface IProductRepository
{
	public function getArrayOfUsedNames($currentProductId = null): Array
}
