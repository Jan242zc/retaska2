<?php

declare(strict_types=1);

namespace App\Services\GeneralServiceInterface;

use App\Entity\Product;

interface IBasketService
{
	public function getAllBasketItems(): Array;
	public function getBasketItemsIds(): Array;
	public function getBasketItemById($id);
	public function addProductToBasket(Product $product, int $quantity);
	public function removeItemFromBasket($id);
	public function removeAllItemsFromBasket();
}
