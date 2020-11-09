<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use Nette;
use App\Controls\Admin\NavbarControl;
use Nette\Security\Permission;


abstract class BaseAdminPresenter extends Nette\Application\UI\Presenter
{
	/** @var Permission @inject */
	public $accessControlList;
	
	protected function startup(): void
	{
		parent::startup();
		if (!$this->getUser()->isLoggedIn() && $this->getName() !== 'Admin:Sign') {
			$this->redirect('Sign:default');
		}
	}
	
	protected function allowOrRedirect($resource, $action = 'read'): void
	{
		if(!$this->accessControlList->isAllowed($this->getUser()->getRoles()[0], $resource, $action)){
			$this->flashMessage('K této akci nemáte potřebná oprávnění.');
			$this->redirect('Homepage:default');
		}
	}
	
	public function createComponentNavbarControl()
	{
		$navbarControl = new NavbarControl();
		$this->onStartup = [$navbarControl, 'render'];
		return $navbarControl;
	}
}
