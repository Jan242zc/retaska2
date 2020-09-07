<?php

declare(strict_types=1);

namespace App\Services\Repository\RepositoryInterface;


interface ICategoryRepository
{
	public function getArrayOfUsedNames($currentCategoryId = null): Array;
	public function findAllForForm(): Array;
}