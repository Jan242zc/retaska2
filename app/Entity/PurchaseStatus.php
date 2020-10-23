<?php

declare(strict_types=1);

namespace App\Entity;


final class PurchaseStatus
{
	/** @var int*/
	private $id;

	/** @var string */
	private $name;
	
	/** @var bool */
	private $defaultForNewPurchases;

	/** @var bool */
	private $meansCancelled;

	public function __construct($id = null, string $name, bool $defaultForNewPurchases, bool $meansCancelled){
		$this->id = $id;
		$this->name = $name;
		$this->defaultForNewPurchases = $defaultForNewPurchases;
		$this->meansCancelled = $meansCancelled;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setId(int $id): void
	{
		$this->id = $id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}
	
	public function getDefaultForNewPurchases(): bool
	{
		return $this->defaultForNewPurchases;
	}

	public function setDefaultForNewPurchases(bool $defaultForNewPurchases): void
	{
		$this->defaultForNewPurchases = $defaultForNewPurchases;
	}

	public function getMeansCancelled(): bool
	{
		return $this->meansCancelled;
	}

	public function setMeansCancelled(bool $meansCancelled): void
	{
		$this->meansCancelled = $meansCancelled;
	}

	public function toArray(): Array
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'default_for_new_purchases' => $this->defaultForNewPurchases ? 1 : 0,
			'means_cancelled' => $this->meansCancelled ? 1 : 0
		];
	}
}
