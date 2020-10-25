<?php

declare(strict_types=1);

namespace App\Services\Repository\RepositoryInterface;


interface IProductRepository
{
	public function findByCategory($category): Array;
	public function getProductsCount(): int;
	public function getProductsCountByCategory($category): int;
	public function findTopProducts($limit): Array;
	public function updateAvailableQuantitiesOfBasketItems(Array $basketItems): Array;
	public function decreaseAvailableAmountsByBasketData(Array $basketItems): int;
}
