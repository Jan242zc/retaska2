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
	private $meansCancelled;

	public function __construct($id = null, string $name, bool $meansCancelled){
		$this->id = $id;
		$this->name = $name;
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
			'means_cancelled' => $this->meansCancelled ? 1 : 0
		];
	}
}
