<?php

declare(strict_types=1);

namespace App\Services\GeneralServiceInterface;


interface IDatabaseReadinessChecker
{
	public function databaseIsReadyToReceivePurchases(): bool;
}
