<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Country;
use App\Entity\DeliveryCountryPaymentPrices;


final class CustomerData
{
	/** @var string */
	private $name;

	/** @var string */
	private $streetAndNumber;

	/** @var string */
	private $city;

	/** @var string */
	private $zip;

	/** @var Country */
	private $country;

	/** @var string */
	private $email;

	/** @var string */
	private $phone;

	/** @var bool */
	private $differentAdress;

	/** @var DeliveryCountryPaymentPrices */
	private $deliveryService;

	/** @var string */
	private $note;

	/** @var string */
	private $deliveryStreetAndNumber;

	/** @var string */
	private $deliveryCity;

	/** @var string */
	private $deliveryZip;

	/** @var Country */
	private $deliveryCountry;

	
	public function __construct($name, $streetAndNumber, $city, $zip, $country, $email, $phone, $differentAdress, $deliveryService, $deliveryStreetAndNumber, $deliveryCity = null, $deliveryZip = null, $deliveryCountry = null, $note = null){
		$this->name = $name;
		$this->streetAndNumber = $streetAndNumber;
		$this->city = $city;
		$this->zip = $zip;
		$this->country = $country;
		$this->email = $email;
		$this->phone = $phone;
		$this->differentAdress = $differentAdress;
		$this->deliveryService = $deliveryService;
		$this->deliveryStreetAndNumber = $deliveryStreetAndNumber;
		$this->deliveryCity = $deliveryCity;
		$this->deliveryZip = $deliveryZip;
		$this->deliveryCountry = $deliveryCountry;
		$this->note = $note;
	}

	public function getName(): string
	{
		return $this->name;
	}
	
	public function setName(string $name): void
	{
		$this->name = $name;
	}
	
	public function getStreetAndNumber(): string
	{
		return $this->streetAndNumber;
	}
	
	public function setStreetAndNumber(string $streetAndNumber): void
	{
		$this->streetAndNumber = $streetAndNumber;
	}
	
	public function getCity(): string
	{
		return $this->city;
	}
	
	public function setCity(string $city): void
	{
		$this->city = $city;
	}
	
	public function getZip(): string
	{
		return $this->zip;
	}
	
	public function setZip(string $zip): void
	{
		$this->zip = $zip;
	}
	
	public function getCountry(): Country
	{
		return $this->country;
	}
	
	public function setCountry(Country $country): void
	{
		$this->country = $country;
	}
	
	public function getEmail(): string
	{
		return $this->email;
	}
	
	public function setEmail(string $email): void
	{
		$this->email = $email;
	}
	
	public function getPhone(): string
	{
		return $this->phone;
	}
	
	public function setPhone(string $phone): void
	{
		$this->phone = $phone;
	}
	
	public function getDifferentAdress(): bool
	{
		return $this->differentAdress;
	}
	
	public function setDifferentAdress(bool $differentAdress): void
	{
		$this->differentAdress = $differentAdress;
	}
	
	public function getDeliveryService(): DeliveryCountryPaymentPrices
	{
		return $this->deliveryService;
	}
	
	public function setDeliveryService(): void
	{
		$this->deliveryService = $deliveryService;
	}
	
	public function getDeliveryStreetAndNumber()
	{
		return $this->deliveryStreetAndNumber;
	}
	
	public function setDeliveryStreetAndNumber(string $deliveryStreetAndNumber): void
	{
		$this->deliveryStreetAndNumber = $deliveryStreetAndNumber;
	}
	
	public function getDeliveryCity()
	{
		return $this->deliveryCity;
	}
	
	public function setDeliveryCity(string $deliveryCity): void
	{
		$this->deliveryCity = $deliveryCity;
	}
	
	public function getDeliveryZip()
	{
		return $this->deliveryZip;
	}
	
	public function setDeliveryZip(string $deliveryZip): void
	{
		$this->deliveryZip = $deliveryZip;
	}
	
	public function getDeliveryCountry()
	{
		return $this->deliveryCountry;
	}
	
	public function setDeliveryCountry(Country $deliveryCountry): void
	{
		$this->deliveryCountry = $deliveryCountry;
	}
	
	public function getNote()
	{
		return $this->note;
	}
	
	public function setNote(string $note): void
	{
		$this->note = $note;
	}
	
	public function toArray(): Array
	{
		$purchaseData = [
			'personalData' => [
				'name' => $this->name,
				'streetAndNumber' => $this->streetAndNumber,
				'city' => $this->city,
				'zip' => $this->zip,
				'country' => $this->country->getId(),
				'email' => $this->email,
				'phone' => $this->phone,
				'differentAdress' => $this->differentAdress
			],
			'deliveryTerms' => [
				'delivery' => $this->deliveryService->getDelivery()->getId(),
				'payment' => $this->deliveryService->getPayment()->getId(),
				'note' => $this->note
			],
		];
		
		if($this->differentAdress){
			$purchaseData['deliveryAdress'] = [
				'streetAndNumber' => $this->deliveryStreetAndNumber,
				'city' => $this->deliveryCity,
				'zip' => $this->deliveryZip,
				'country' => $this->deliveryCountry->getId()
			];
		}
		
		return $purchaseData;
	}
}
