<?php

declare(strict_types=1);

namespace App\Modules\Admin\Presenters;

use Nette;
use App\Modules\Admin\Presenters\BaseAdminPresenter as BasePresenter;
use Nette\Application\UI\Form;
use App\Services\Repository\RepositoryInterface\IRepository;
use App\Services\Repository\RepositoryInterface\IEntityRepository;


final class EntityPresenter extends BasePresenter
{
	private $entityRepository;
	
	public function __construct(IEntityRepository $entityRepository){
		$this->entityRepository = $entityRepository;
	}
	
	public function renderDefault(): void
	{
		$this->template->entities = $this->entityRepository->findAll();
	}
	
	public function manage($id): void
	{
		
	}
	
	public function createComponentManageEntityForm(): Form
	{
		$form = new Form;
		$form->setHtmlAttribute('class', 'form');
		
		$form->addHidden('id');
		
		$form->addText('name', 'Název:')
			->setDisabled(true);
		
		$form->addText('nameCzech', 'Název česky:')
			->setDisabled(true);
			
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
		dump($data);
		exit;
	}
}
