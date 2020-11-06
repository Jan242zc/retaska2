<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use Nette;
use App\Modules\Admin\Presenters\BaseAdminPresenter as BasePresenter;
use App\Services\Repository\RepositoryInterface\IPurchaseItemRepository;


final class HomepagePresenter extends BasePresenter
{
	/** @var IPurchaseItemRepository */
	private $purchaseItemRepository;
	
	public function __construct(IPurchaseItemRepository $purchaseItemRepository){
		$this->purchaseItemRepository = $purchaseItemRepository;
	}
	
	public function renderDefault(): void
	{
		$this->template->top10LastWeek = $this->purchaseItemRepository->findXMostSoldInTheLastXDays(10, 7);
	}
}
