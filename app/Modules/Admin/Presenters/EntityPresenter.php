<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use Nette;
use App\Modules\Admin\Presenters\BaseAdminPresenter as BasePresenter;
use Nette\Application\UI\Form;
use App\Services\Repository\RepositoryInterface\IRepository;
use App\Services\Repository\RepositoryInterface\IEntityRepository;
use App\Entity\Factory\EntityFactory;


final class EntityPresenter extends BasePresenter
{
	private const RESOURCE = 'generalAdmin';

	private $entityRepository;
	private $entityFactory;
	
	public function __construct(IEntityRepository $entityRepository, EntityFactory $entityFactory){
		$this->entityRepository = $entityRepository;
		$this->entityFactory = $entityFactory;
	}
	
	public function renderDefault(): void
	{
		$this->allowOrRedirect(self::RESOURCE);
		$this->template->entities = $this->entityRepository->findAll();
	}
	
	public function actionManage($id): void
	{
		$this->allowOrRedirect(self::RESOURCE);
		try{
			$entity = $this->entityRepository->find($id);
		} catch (\Exception $ex) {
			$this->flashMessage('Entita nenalezena');
			$this->redirect('Entity:default');
		}
		$this->template->entity = $entity;
		$this['manageEntityForm']->setDefaults($entity->toArray());
	}
	
	public function createComponentManageEntityForm(): Form
	{
		$form = new Form;
		$form->setHtmlAttribute('class', 'form');
		
		$form->addHidden('id')
			->addFilter(function($value){
				return intval($value);
			});
		
		$form->addHidden('name', 'Název:'); //hidden instead of disabled, as I need it sent data (vanilla JS could help, but too complicated)
		
		$form->addHidden('nameCzech', 'Název česky:'); //dtto
			
		$form->addText('idLimit', 'Limit ID:')
			->setRequired('Je třeba uvést limit.')
			->addRule($form::NUMERIC, 'Limit musí být celé kladné číslo.')
			->addFilter(function($value){
				return intval($value);
			});
			
		$form->addSubmit('submit', 'Uložit')
			->setHtmlAttribute('class', 'submit');
		
		$form->onSuccess[] = [$this, 'manageEntityFormSucceeded'];
		
		return $form;
	}
	
	public function manageEntityFormSucceeded(Form $form, Array $data): void
	{
		$entity = $this->entityFactory->createFromArray($data);

		if($this->entityRepository->update($entity) === 1){
			$this->flashMessage('Změny uloženy.');
		} else {
			$this->flashMessage('Něco se pokazilo.');
		}
		$this->redirect('Entity:default');
	}
}
