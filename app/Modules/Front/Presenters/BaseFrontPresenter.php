<?php

declare(strict_types=1);

namespace App\Modules\Front\Presenters;

use Nette;
use App\Controls\Front\NavbarControl;
use App\Controls\Front\Factory\FrontNavbarFactory;


abstract class BaseFrontPresenter extends Nette\Application\UI\Presenter
{
	public function createComponentNavbarControl(): NavbarControl
	{
		$navbarControl = new NavbarControl();
		$this->onStartup = [$navbarControl, 'render'];
		return $navbarControl;
	}
}
