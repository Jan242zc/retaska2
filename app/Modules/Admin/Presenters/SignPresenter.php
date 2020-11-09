<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use App\Modules\Admin\Presenters\BaseAdminPresenter AS BasePresenter;
use Nette\Application\UI\Form;
use Nette\Security\IAuthenticator;
use Nette\Security\AuthenticationException;


final class SignPresenter extends BasePresenter
{
	/** @var IAuthenticator */
	private $authenticator;
	
	public function __construct(IAuthenticator $authenticator){
		$this->authenticator = $authenticator;
	}
	
	public function renderDefault(): void
	{
		
	}
	
	protected function createComponentSignInForm(): Form
	{
		$form = new Form();
		$form->setHtmlAttribute('class', 'form sign-form');
		
		$form->addText('name', 'Uživatelské jméno:')
			->setRequired('Nemůžete se přihlásit bez zadání uživatelského jména.');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Nemůžete se přihlásit bez zadání hesla.');

		$form->addSubmit('send', 'Přihlásit se')
			->setHtmlAttribute('class', 'submit');

		$form->onSuccess[] = [$this, 'signInFormSucceeded'];

		return $form;
	}
	
	public function signInFormSucceeded(Form $form, Array $data): void
	{
		try{
			$this->getUser()->login($this->authenticator->authenticate($data));
			$this->flashMessage('Byli jste úspěšně přihlášeni.');
			$this->redirect('Homepage:default');
		} catch (AuthenticationException $ex){
			$this->flashMessage('Přihlášení se nepodařilo. Zkontrolujte přihlašovací údaje.');
		}
	}
	
	public function actionOut(): void
	{
		$this->getUser()->logout();
		$this->redirect('Sign:default');
	}	
}
