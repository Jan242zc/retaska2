<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use Nette;
use App\Controls\Admin\NavbarControl;


abstract class BaseAdminPresenter extends Nette\Application\UI\Presenter
{
	protected function startup(): void
	{
		parent::startup();
		if (!$this->getUser()->isLoggedIn() && $this->getName() !== 'Admin:Sign') {
			$this->redirect('Sign:default');
		}
	}
	
	public function createComponentNavbarControl()
	{
		$navbarControl = new NavbarControl();
		$this->onStartup = [$navbarControl, 'render'];
		return $navbarControl;
	}
}
