<?php

declare(strict_types=1);

namespace App\Services\Repository\RepositoryInterface;

use App\Entity\UserData;


interface IUserDataRepository
{
	public function findByName(string $name): UserData;
}
