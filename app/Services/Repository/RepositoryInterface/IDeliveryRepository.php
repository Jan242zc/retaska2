<?php

declare(strict_types=1);

namespace App\Services\Repository\RepositoryInterface;

use App\Entity\Delivery;

interface IDeliveryRepository
{
	public function findById(int $id);
	public function findAllForForm(): Array;
}
