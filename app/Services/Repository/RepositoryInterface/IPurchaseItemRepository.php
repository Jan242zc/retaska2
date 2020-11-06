<?php

declare(strict_types=1);

namespace App\Services\Repository\RepositoryInterface;


interface IPurchaseItemRepository
{
	public function insertMultiple(int $purchaseId, array $items): int;
	public function findByPurchaseId(int $purchaseId): Array;
	public function findXMostSoldInTheLastXDays(int $limit, int $days): Array;
}
