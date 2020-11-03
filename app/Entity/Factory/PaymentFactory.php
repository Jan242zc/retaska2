<?php

declare(strict_types=1);

namespace App\Entity\Factory;

use App\Entity\Payment;


final class PaymentFactory
{
	public function __construct(){
		
	}
	
	public function createFromArray(Array $data): Payment
	{
		if(!$id = $data['id']){
			$id = null;
		}
		return new Payment($id, $data['name']);
	}
	
	public function createFromObject($object): Payment
	{
		if(!$id = $object->id){
			$id = null;
		}
		return new Payment($id, $object->name);
	}
}
