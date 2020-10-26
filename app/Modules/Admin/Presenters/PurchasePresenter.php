<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use App\Modules\Admin\Presenters\BaseAdminPresenter AS BasePresenter;
use App\Services\Repository\RepositoryInterface\IPurchaseRepository;


final class PurchasePresenter extends BasePresenter
{
	/** @var IPurchaseRepository */
	private $purchaseRepository;
	
	public function __construct(IPurchaseRepository $purchaseRepository){
		$this->purchaseRepository = $purchaseRepository;
	}
	
	public function renderDefault(): void
	{
		$this->template->purchases = $this->purchaseRepository->findAll();
	}
}
