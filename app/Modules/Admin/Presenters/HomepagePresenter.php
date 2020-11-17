<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use Nette;
use App\Modules\Admin\Presenters\BaseAdminPresenter as BasePresenter;
use App\Services\Repository\RepositoryInterface\IPurchaseItemRepository;
use App\Controls\Admin\Factory\IEmptyTablesWarningFactory;
use App\Controls\Admin\EmptyTablesWarning;


final class HomepagePresenter extends BasePresenter
{
	private const RESOURCE = 'generalAdmin';

	/** @var IPurchaseItemRepository */
	private $purchaseItemRepository;
	
	/** @var IEmptyTablesWarningFactory */
	private $emptyTablesWarningFactory;
	
	public function __construct(IPurchaseItemRepository $purchaseItemRepository, IEmptyTablesWarningFactory $emptyTablesWarningFactory){
		$this->purchaseItemRepository = $purchaseItemRepository;
		$this->emptyTablesWarningFactory = $emptyTablesWarningFactory;
	}
	
	public function renderDefault(): void
	{
		$this->allowOrRedirect(self::RESOURCE);		
		$this->template->top10LastWeek = $this->purchaseItemRepository->findXMostSoldInTheLastXDays(10, 7);
		$this->template->top10LastMonth = $this->purchaseItemRepository->findXMostSoldInTheLastXDays(10, 30);

		$today = new \DateTime();
		$beginning = new \DateTime('2020-09-02');
		$daysSinceTheBeginning = $beginning->diff($today)->days;
		$this->template->top10Ever = $this->purchaseItemRepository->findXMostSoldInTheLastXDays(10, $daysSinceTheBeginning);
	}

	protected function createComponentEmptyTablesWarning(): EmptyTablesWarning
	{
		return $this->emptyTablesWarningFactory->create();		
	}
}
