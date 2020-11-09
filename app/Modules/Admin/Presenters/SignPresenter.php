<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use App\Modules\Admin\Presenters\BaseAdminPresenter AS BasePresenter;
use Nette\Application\UI\Form;


final class SignPresenter extends BasePresenter
{
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
	
	public function signInFormSucceeded(Form $form, \stdClass $values): void
	{
		dump($values);
		exit;
	}
}
