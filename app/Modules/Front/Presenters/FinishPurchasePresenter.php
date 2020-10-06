<?php

declare(strict_types=1);

namespace App\Modules\Front\Presenters;

use App\Modules\Front\Presenters\BaseFrontPresenter AS BasePresenter;
use App\Services\GeneralServiceInterface\IBasketService;


final class FinishPurchasePresenter extends BasePresenter
{
	/** @var IBasketService */
	private $basketService;
	
	public function __construct(IBasketService $basketService){
		$this->basketService = $basketService;
	}
	
	public function renderDefault(): void
	{
		$this->template->basketItems = $this->basketService->getAllBasketItems();
	}
}
