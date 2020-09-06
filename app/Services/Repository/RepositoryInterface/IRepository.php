<?php

declare(strict_types=1);

namespace App\Services\Repository\RepositoryInterface;


interface IRepository
{
	public function findAll(): Array;
	public function find(string $identification);
	public function update($object);
}
