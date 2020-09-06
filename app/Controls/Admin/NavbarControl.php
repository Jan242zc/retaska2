<?php

namespace App\Controls\Admin;

use Nette; 
use Nette\Application\UI\Control;


final class NavbarControl extends Control
{
	public function render(): void
	{
		$this->template->render(__DIR__ . '/templates/navbar.latte');
	}
}
