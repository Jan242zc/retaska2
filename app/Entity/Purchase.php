<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Country;
use App\Entity\PurchaseStatus;


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
	
	/** @var string */
	private $deliveryName;
	
	/** @var float */
	private $deliveryPrice;
	
	/** @var string */
	private $paymentName;
	
	/** @var float */
	private $paymentPrice;
	
	/** @var float */
	private $totalPrice;
	
	/** @var bool */
	private $shipToOtherThanCustomerAdress;
	
	/** @var \DateTime */
	private $createdAt;
	
	/** @var PurchaseStatus */
	private $purchaseStatus;
	
	/** @var array */
	private $purchaseItems;

	/** @var string */
	private $deliveryStreetAndNumber;
	
	/** @var string */
	private $deliveryCity;
	
	/** @var string */
	private $deliveryZip;

	/** @var Country */
	private $deliveryCountry;
	
	/** @var string */
	private $note;
	
	public function __construct(int $id = null, string $customerName, string $customerStreetAndNumber, string $customerCity, string $customerZip, Country $customerCountry, string $email, string $phone, string $deliveryName, float $deliveryPrice, string $paymentName, float $paymentPrice, float $totalPrice, bool $shipToOtherThanCustomerAdress, \DateTime $createdAt = null, PurchaseStatus $purchaseStatus = null, array $purchaseItems = null, string $deliveryStreetAndNumber = null, string $deliveryCity = null, string $deliveryZip = null, Country $deliveryCountry = null, string $note = null){
		$this->id = $id;
		$this->customerName = $customerName;
		$this->customerStreetAndNumber = $customerStreetAndNumber;
		$this->customerCity = $customerCity;
		$this->customerZip = $customerZip;
		$this->customerCountry = $customerCountry;
		$this->email = $email;
		$this->phone = $phone;
		$this->deliveryName = $deliveryName;
		$this->deliveryPrice = $deliveryPrice;
		$this->paymentName = $paymentName;
		$this->paymentPrice = $paymentPrice;
		$this->totalPrice = $totalPrice;
		$this->shipToOtherThanCustomerAdress = $shipToOtherThanCustomerAdress;
		$this->createdAt = $createdAt;
		$this->purchaseStatus = $purchaseStatus;
		$this->purchaseItems = $purchaseItems;
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
	
	public function getDeliveryName(): string
	{
		return $this->deliveryName;
	}
	
	public function setDeliveryName(string $deliveryName): void
	{
		$this->deliveryName = $deliveryName;
	}
	
	public function getDeliveryPrice(): float
	{
		return $this->deliveryPrice;
	}
	
	public function setDeliveryPrice(float $deliveryPrice): void
	{
		$this->deliveryPrice = $deliveryPrice;
	}
	
	public function getPaymentName(): string
	{
		return $this->paymentName;
	}
	
	public function setPaymentName(string $paymentName): void
	{
		$this->paymentName = $paymentName;
	}
	
	public function getPaymentPrice(): float
	{
		return $this->paymentPrice;
	}
	
	public function setPaymentPrice(float $paymentPrice): void
	{
		$this->paymentPrice = $paymentPrice;
	}
	
	public function getTotalPrice(): float
	{
		return $this->totalPrice;
	}
	
	public function setTotalPrice(float $totalPrice): void
	{
		$this->totalPrice = $totalPrice;
	}
	
	public function getShipToOtherThanCustomerAdress(): bool
	{
		return $this->shipToOtherThanCustomerAdress;
	}
	
	public function setShipToOtherThanCustomerAdress(bool $shipToOtherThanCustomerAdress): void
	{
		$this->shipToOtherThanCustomerAdress = $shipToOtherThanCustomerAdress;
	}
	
	public function getCreatedAt()
	{
		return $this->createdAt;
	}
	
	public function setCreatedAt(\DateTime $createdAt): void
	{
		$this->createdAt = $createdAt;
	}
		
	public function getPurchaseStatus()
	{
		return $this->purchaseStatus;
	}
	
	public function setPurchaseStatus(PurchaseStatus $purchaseStatus): void
	{
		$this->purchaseStatus = $purchaseStatus;
	}
		
	public function getPurchaseItems()
	{
		return $this->purchaseItems;
	}
	
	public function setPurchaseItems(array $purchaseItems): void
	{
		$this->purchaseItems = $purchaseItems;
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
	
	public function setDeliveryCountry(string $deliveryCountry): void
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
		$array = [
			'id' => $this->id,
			'customerName' => $this->customerName,
			'customerStreetAndNumber' => $this->customerStreetAndNumber,
			'customerCity' => $this->customerCity,
			'customerZip' => $this->customerZip,
			'customerCountry' => $this->customerCountry->getId(),
			'email' => $this->email,
			'phone' => $this->phone,
			'deliveryName' => $this->deliveryName,
			'deliveryPrice' => $this->deliveryPrice,
			'paymentName' => $this->paymentName,
			'paymentPrice' => $this->paymentPrice,
			'totalPrice' => $this->totalPrice,
			'shipToOtherThanCustomerAdress' => $this->shipToOtherThanCustomerAdress,
			'created_at' => null,
			'purchaseStatus_id' => null,
			'deliveryStreetAndNumber' => null,
			'deliveryCity' => null,
			'deliveryZip' => null,
			'deliveryCountry' => null,
			'note' => null
		];
		
		if($this->shipToOtherThanCustomerAdress){
			$array['deliveryStreetAndNumber'] = $this->deliveryStreetAndNumber;
			$array['deliveryCity'] = $this->deliveryCity;
			$array['deliveryZip'] = $this->deliveryZip;
			$array['deliveryCountry'] = $this->deliveryCountry->getId();
		}
		
		if(!is_null($this->purchaseStatus)){
			$array['created_at'] = $this->createdAt;
		}
		
		if(!is_null($this->purchaseStatus)){
			$array['purchaseStatus_id'] = $this->purchaseStatus->getId();
		}
		
		if(!is_null($this->note)){
			$array['note'] = $this->note;
		}
		
		return $array;
	}

	public function itemsToProductIdQuantityArray(): Array
	{
		$array = [];

		foreach($this->purchaseItems as $item){
			$array[] = [
				'product_id' => $item->getProductId(),
				'quantity' => $item->getQuantity()
			];
		}

		return $array;
	}	
}
