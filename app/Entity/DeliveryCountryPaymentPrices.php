<?php

declare(strict_types=1);

namespace App\Entity;

use Nette;
use App\Entity\Delivery;
use App\Entity\Country;
use App\Entity\Payment;


final class DeliveryCountryPaymentPrices
{
	/** @var int */
	private $id;
	
	/** @var Delivery */
	private $delivery;
	
	/** @var Country */
	private $country;
	
	/** @var Payment */
	private $payment;
	
	/** @var float */
	private $deliveryPrice;
	
	/** @var float */
	private $paymentPrice;
	
	public function __construct(int $id = null, Delivery $delivery, Payment $payment, float $deliveryPrice, float $paymentPrice, Country $country = null){
		$this->id = $id;
		$this->delivery = $delivery;
		$this->payment = $payment;
		$this->deliveryPrice = $deliveryPrice;
		$this->paymentPrice = $paymentPrice;
		$this->country = $country;
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function setId(int $id): void
	{
		$this->id = $id;
	}
	
	public function getDelivery(): Delivery
	{
		return $this->delivery;
	}
	
	public function setDelivery(Delivery $delivery): void
	{
		$this->delivery = $delivery;
	}
	
	public function getCountry()
	{
		return $this->country;
	}
	
	public function setCountry(Country $country): void
	{
		$this->country = $country;
	}
	
	public function getPayment(): Payment
	{
		return $this->payment;
	}
	
	public function setPayment(Payment $payment): void
	{
		$this->payment = $payment;
	}
	
	public function getDeliveryPrice(): float
	{
		return $this->deliveryPrice;
	}
	
	public function setDeliveryPrice(float $deliveryPrice): void
	{
		$this->deliveryPrice = $deliveryPrice;
	}
	
	public function getPaymentPrice(): float
	{
		return $this->paymentPrice;
	}
	
	public function setPaymentPrice(float $paymentPrice): void
	{
		$this->paymentPrice = $paymentPrice;
	}
	
	public function toArray(): Array
	{
		$array =  [
			'id' => $this->id,
			'delivery' => $this->delivery->getId(),
			'payment' => $this->payment->getId(),
			'deliveryPrice' => $this->deliveryPrice,
			'paymentPrice' => $this->paymentPrice
		];
		
		if(isset($this->country)){
			$array['country'] = $this->country->getId();
		}
		
		return $array;
	}
}
