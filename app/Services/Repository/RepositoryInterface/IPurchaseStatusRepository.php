<?php

declare(strict_types=1);

namespace App\Services\Repository\RepositoryInterface;

use App\Entity\PurchaseStatus;


interface IPurchaseStatusRepository
{
	public function findAllForNewDefaultForm(): Array;
	public function setDefaultStatusForNewPurchases(PurchaseStatus $purchaseStatus): int;
	public function findDefaultStatusForNewPurchases(): PurchaseStatus;
}
