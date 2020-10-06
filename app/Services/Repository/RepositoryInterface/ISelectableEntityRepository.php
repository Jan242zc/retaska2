<?php

declare(strict_types=1);

namespace App\Services\Repository\RepositoryInterface;


interface ISelectableEntityRepository //aka an entity we want to use in select field in a form
{
	public function findAllForForm(): Array;
}
