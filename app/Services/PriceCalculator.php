<?php

declare(strict_types=1);

namespace App\Services;


final class PriceCalculator
{
	//this may seem silly, it's here for the case of price calculation getting trickier
	public static function calculateProductTotalPrice(int $quantity, float $price): float
	{
		return $quantity * $price;
	}

	public static function calculateAllProductsTotalPrice(Array $basketContents): float
	{
		$totalPrice = 0;
		foreach($basketContents as $item){
			$totalPrice += $item->getPrice();
		}
		return $totalPrice;
	}
	
	public static function calculateTotalPurchasePrice(float $totalProductsPrice, float $deliveryPrice, float $paymentPrice): float
	{
		return intval($totalProductsPrice + $deliveryPrice + $paymentPrice); //rounding down
	}
}
