<?php

class CompanyAliases extends \Phalcon\Mvc\Model {
	public $Id;
	public $UserId;
	public $Alias;
	
	public function getSource(){
		return "company_aliases";
	}
}
