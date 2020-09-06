<?php

declare(strict_types=1);

namespace App\Services\Repository\RepositoryInterface;


interface ICreatableAndDeleteableEntityRepository
{
	public function insert($category);
	public function delete(string $identification);
}
