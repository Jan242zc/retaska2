<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use App\Modules\Admin\Presenters\BaseAdminPresenter AS BasePresenter;
use App\Services\Repository\RepositoryInterface\IUserDataRepository;
use Nette\Security\IAuthenticator;
use App\Entity\Factory\UserDataFactory;
use Nette\Application\UI\Form;
use Nette\Security\Passwords;


final class MyAccountPresenter extends BasePresenter
{
	/** @var IUserDataRepository */
	private $userDataRepository;

	/** @var Passwords */
	private $passwords;

	public function __construct(IUserDataRepository $userDataRepository, Passwords $passwords){
		$this->userDataRepository = $userDataRepository;
		$this->passwords = $passwords;
	}
	
	public function renderDefault(): void
	{
		
	}
	
	public function actionChangePassword(): void
	{
		
	}
	
	protected function createComponentChangePasswordForm(): Form
	{
		$form = new Form();
		$form->setHtmlAttribute('class', 'form');

		$form->addPassword('theOldOne', 'Staré heslo:');

		$form->addPassword('theNewOne', 'Nové heslo:')
			->addRule($form::MIN_LENGTH, 'Heslo musí mít nejměně %d znaků.', 4);

		$form->addPassword('theNewOneAgain', 'Ověření nového hesla:')
			->addRule($form::EQUAL, 'Nová hesla se neshodují.', $form['theNewOne']);

		$form->addSubmit('save', 'Změnit heslo')
			->setHtmlAttribute('class', 'submit');

		$form->onSuccess[] = [$this, 'changePasswordFormSucceeded'];

		return $form;
	}
	
	public function changePasswordFormSucceeded(Form $form, Array $data): void
	{
		$userData = $this->userDataRepository->findById(intval($this->getUser()->getId()));
		if(!$this->passwords->verify($data['theOldOne'], $userData->getPassword())){
			$this->flashMessage('Nesprávné staré heslo.');
			$this->redirect('this');
		}
		
		$userData->setPassword($this->passwords->hash($data['theNewOne']));
		
		if($this->userDataRepository->update($userData) === 1){
			$this->flashMessage('Heslo změněno.');
		} else {
			$this->flashMessage('Došlo k chybě.');
		}
		$this->redirect('MyAccount:default');
	}
}
