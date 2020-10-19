<?php

declare(strict_types=1);

namespace App\Services\GeneralServiceInterface;

use App\Entity\Product;
use App\Entity\BasketItem;


interface IBasketService
{
	public function getAllBasketItems(): Array;
	public function getBasketItemsIds(): Array;
	public function getBasketItemById($id): BasketItem;
	public function addProductToBasket(Product $product, int $quantity): void;
	public function removeItemFromBasket($id): void;
	public function removeAllItemsFromBasket(): void;
	public function adjustBasketByBasketFormData(Array $idValuesArray, Array $quantitiesArray, Array $toBeDeletedValuesArray): void;
	public function verifyThatThisItemInBasket(int $id): bool;
	public function verifyThatAllTheseItemsInBasket(array $ids): bool;
	public function getTotalProductPrice(): float;
	public function checkAvailibility(): void;
}
