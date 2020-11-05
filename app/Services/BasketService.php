<?php

declare(strict_types=1);

namespace App\Services;

use Nette;
use Nette\Http\Session AS Session;
use App\Entity\Basket;
use App\Entity\BasketItem;
use App\Entity\Product;
use App\Entity\CustomerData;
use App\Services\GeneralServiceInterface\IBasketService;
use App\Services\PriceCalculator;
use App\Services\Repository\RepositoryInterface\IProductRepository;


final class BasketService implements IBasketService
{
	/** @var Basket */
	private $basketSessionSection;
	
	/** @var IProductRepository */
	private $productRepository;
	
	public function __construct(Session $session, IProductRepository $productRepository){
		$this->basketSessionSection = $this->getThisServicesSessionSection($session);
		$this->productRepository = $productRepository;
	}
	
	private function getThisServicesSessionSection($session) //what to return?
	{
		$sessionSection = $session->getSection('basket');
		if(!isset($sessionSection->basket)){
			$sessionSection->basket = new Basket([]);
		}
		if(!$sessionSection->basket->itemsSet()){
			$sessionSection->basket->setItems([]);
		}
		return $sessionSection->basket;
	}
	
	public function getAllBasketItems(): Array
	{
		return $this->basketSessionSection->getItems();
	}
	
	//currently used only by this class
	public function getBasketItemsIds(): Array
	{
		return $this->basketSessionSection->getItemsIds();
	}
	
	public function getBasketItemById($id): BasketItem
	{
		return $this->basketSessionSection->getItemById($id);
	}
	
	public function addProductToBasket(Product $product, int $quantity): void
	{
		$price = PriceCalculator::calculateProductTotalPrice($quantity, $product->getPrice());
		$this->basketSessionSection->addItem($product, $quantity, $price);
		$this->updateAllProductsTotalPrice();
		$this->updateTotalPurchasePrice();
	}
	
	//currently used only by this class
	public function removeItemFromBasket($id): void
	{
		if(in_array($id, $this->getBasketItemsIds())){
			$this->basketSessionSection->removeItem($id);
			$this->updateAllProductsTotalPrice();
		}
	}
	
	public function removeAllItemsFromBasket(): void
	{
		$this->basketSessionSection->removeAllItems();
		$this->updateAllProductsTotalPrice();
	}
	
	public function adjustBasketByBasketFormData(Array $idValuesArray, Array $quantitiesArray, Array $toBeDeletedValuesArray): void
	{
		$this->adjustProductsQuantities($idValuesArray, $quantitiesArray);
		$this->removeTheseItemsFromBasket($toBeDeletedValuesArray);
		$this->updateAllProductsTotalPrice();
		$this->updateTotalPurchasePrice();
	}
	
	private function removeTheseItemsFromBasket(Array $toBeDeletedIds): void
	{
		foreach($toBeDeletedIds as $id){
			$this->removeItemFromBasket(intval($id));
		}
	}
	
	private function adjustProductsQuantities(Array $ids, Array $quantities): void
	{
		foreach($ids as $key => $id){
			$product = $this->getBasketItemById($id);
			$product->setQuantity(intval($quantities[$key]));
			$productPriceInBasket = PriceCalculator::calculateProductTotalPrice(intval($quantities[$key]), $product->getProduct()->getPrice());
			$product->setPrice($productPriceInBasket);
		}
	}
	
	private function updateAllProductsTotalPrice(): void
	{
		$this->basketSessionSection->setTotalPrice(PriceCalculator::calculateAllProductsTotalPrice($this->basketSessionSection->getItems()));
	}
	
	private function updateTotalPurchasePrice(): void
	{
		$productsPrice = $this->basketSessionSection->getTotalPrice();
		$deliveryPrice = $this->basketSessionSection->getCustomerData()->getDeliveryService()->getDeliveryPrice();
		$paymentPrice = $this->basketSessionSection->getCustomerData()->getDeliveryService()->getPaymentPrice();
		$this->basketSessionSection->setTotalPurchasePrice(PriceCalculator::calculateTotalPurchasePrice($productsPrice, $deliveryPrice, $paymentPrice));
	}
	
	//currently not used anywhere
	public function verifyThatThisItemInBasket($id): bool
	{
		return in_array(intval($id), $this->getBasketItemsIds());
	}
	
	public function verifyThatAllTheseItemsInBasket(array $ids): bool
	{
		$idsInBasket = $this->getBasketItemsIds();
		foreach($ids as $id){
			if(!in_array($id, $idsInBasket)){
				return false;	
			}
		}
		return true;
	}
	
	public function getTotalProductPrice(): float
	{
		return $this->basketSessionSection->getTotalPrice();
	}
	
	public function getTotalPurchasePrice(): float
	{
		return $this->basketSessionSection->getTotalPurchasePrice();
	}
	
	public function updateAvailableAmountsOfItems(): void
	{
		foreach($this->getBasketItemsIds() as $id){
			$this->updateAvailableAmountOfItem($id);
		}
	}

	private function updateAvailableAmountOfItem($id): void
	{
		$currentProductInDatabase = $this->productRepository->findById($id);
		$this->basketSessionSection->getItemById($id)->getProduct()->setAmountAvailable($currentProductInDatabase->getAmountAvailable());
	}

	public function anyItemUnavailable(): bool
	{
		foreach($this->getBasketItemsIds() as $id){
			$this->updateAvailableAmountOfItem($id);
			$item = $this->basketSessionSection->getItemById($id);
			if($item->getProduct()->getAmountAvailable() < $item->getQuantity()){
				return true;
			}
		}
		return false;
	}

	public function checkAvailibility(): void
	{
		//update available amount of the products in basket
		$this->updateAvailableAmountsOfItems();

		foreach($this->getBasketItemsIds() as $id){
			$item = $this->basketSessionSection->getItemById($id);
			if($item->getProduct()->getAmountAvailable() < $item->getQuantity()){
				$item->setRequstedQuantityNotAvailable(true);
			}
		}
	}
	
	public function deleteZeros(): void
	{
		foreach($this->getBasketItemsIds() as $id){
			$item = $this->basketSessionSection->getItemById($id);
			if($item->getQuantity() === 0){
				$this->removeItemFromBasket($id);
			}
		}
	}
	
	public function getCustomerData()
	{
		return $this->basketSessionSection->getCustomerData();
	}
	
	public function setCustomerData(CustomerData $customerData): void
	{
		$this->basketSessionSection->setCustomerData($customerData);
		$this->updateTotalPurchasePrice();
	}
	
	public function getPricesPerProductOfBasketItems(): Array
	{
		$prices = [];

		foreach($this->getAllBasketItems() as $item){
			$prices[$item->getProduct()->getId()] = $item->getProduct()->getPrice();
		}

		return $prices;
	}
	
	public function deleteAllData(): void
	{
		$this->basketSessionSection->deleteCustomerDataAndBasketItems();
	}
}
