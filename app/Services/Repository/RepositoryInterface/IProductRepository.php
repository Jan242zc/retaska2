<?php

declare(strict_types=1);

namespace App\Services\Repository\RepositoryInterface;


interface IProductRepository
{
	public function findByCategory($category): Array;
	public function getArrayOfUsedNames($currentProductId = null): Array;
	public function getProductsCount(): int;
	public function getProductsCountByCategory($category): int;
	public function findTopProducts($limit): Array;
}
