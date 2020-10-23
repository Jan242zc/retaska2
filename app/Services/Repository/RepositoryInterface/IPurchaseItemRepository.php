<?php

declare(strict_types=1);

namespace App\Services\Repository\RepositoryInterface;


interface IPurchaseItemRepository
{
	public function insertMultiple(int $purchaseId, array $items): int;
}
