<?php

declare(strict_types=1);

namespace App\Services\Repository\RepositoryInterface;

use App\Entity\DeliveryCountryPaymentPrices;


interface IDeliveryCountryPaymentPricesRepository
{
	public function findByDefiningStuff(int $deliveryId, int $paymentId, bool $countryIgnorable, int $countryId = null): DeliveryCountryPaymentPrices;
}
