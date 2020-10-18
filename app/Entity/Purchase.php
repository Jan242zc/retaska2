<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Country;


final class Purchase
{
	/** @var int */
	private $id;

	/** @var string */
	private $customerName;

	/** @var string */
	private $customerStreetAndNumber;

	/** @var string */
	private $customerCity;

	/** @var string */
	private $customerZip;

	/** @var Country */
	private $customerCountry;

	/** @var string */
	private $email;

	/** @var string */
	private $phone;

	/** @var bool */
	private $shipToOtherThanPersonalAdress;

	/** @var string */
	private $deliveryStreetAndNumber;

	/** @var string */
	private $deliveryCity;

	/** @var string */
	private $deliveryZip;

	/** @var Country */
	private $deliveryCountry;

	/** @var string */
	private $deliveryService;

	/** @var string */
	private $note;
	
	public function __construct($id = null, $customerName, $customerStreetAndNumber, $customerCity, $customerZip, $customerCountry, $email, $phone, $shipToOtherThanPersonalAdress, $deliveryService, $deliveryStreetAndNumber = null, $deliveryCity = null, $deliveryZip = null, $deliveryCountry = null, $note = null){
		$this->id = $id;
		$this->customerName = $customerName;
		$this->customerStreetAndNumber = $customerStreetAndNumber;
		$this->customerCity = $customerCity;
		$this->customerZip = $customerZip;
		$this->customerCountry = $customerCountry;
		$this->email = $email;
		$this->phone = $phone;
		$this->shipToOtherThanPersonalAdress = $shipToOtherThanPersonalAdress;
		$this->deliveryService = $deliveryService;
		$this->deliveryStreetAndNumber = $deliveryStreetAndNumber;
		$this->deliveryCity = $deliveryCity;
		$this->deliveryZip = $deliveryZip;
		$this->deliveryCountry = $deliveryCountry;
		$this->note = $note;
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function setId(int $id): void
	{
		$this->id = $id;
	}
	
	public function getCustomerName(): string
	{
		return $this->customerName;
	}
	
	public function setCustomerName(string $customerName): void
	{
		$this->customerName = $customerName;
	}
	
	public function getCustomerStreetAndNumber(): string
	{
		return $this->customerStreetAndNumber;
	}
	
	public function setCustomerStreetAndNumber(string $customerStreetAndNumber): void
	{
		$this->customerStreetAndNumber = $customerStreetAndNumber;
	}
	
	public function getCustomerCity(): string
	{
		return $this->customerCity;
	}
	
	public function setCustomerCity(string $customerCity): void
	{
		$this->customerCity = $customerCity;
	}
	
	public function getCustomerZip(): string
	{
		return $this->customerZip;
	}
	
	public function setCustomerZip(string $customerZip): void
	{
		$this->customerZip = $customerZip;
	}
	
	public function getCustomerCountry(): Country
	{
		return $this->customerCountry;
	}
	
	public function setCustomerCountry(Country $customerCountry): void
	{
		$this->customerCountry = $customerCountry;
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
	
	public function getShipToOtherThanPersonalAdress(): bool
	{
		return $this->shipToOtherThanPersonalAdress;
	}
	
	public function setShipToOtherThanPersonalAdress(bool $shipToOtherThanPersonalAdress): void
	{
		$this->shipToOtherThanPersonalAdress = $shipToOtherThanPersonalAdress;
	}
	
	public function getDeliveryService(): string
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
	
}
