<?php

declare(strict_types=1);

namespace App\Services\Repository\RepositoryInterface;

use App\Entity\Country;

interface ICountryRepository
{
	public function findById(int $id);
	public function findAllForForm(): Array;
}
