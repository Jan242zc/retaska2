<?php

namespace App\Services\GeneralServiceInterface;


interface IDeliveryCountryPaymentPricesArrayGenerator
{
	public function generateByDeliveryArray(): Array;
	public function generateByCountryArray(): Array;
	public function generateCountryIndependentServicesArray(): Array;
}
