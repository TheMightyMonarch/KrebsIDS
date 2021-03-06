<?php

use Phalcon\Mvc\Model\Validator\Uniqueness,
	Phalcon\Mvc\Model\Validator\StringLength,
	Phalcon\Mvc\Model\Validator\Email;

class Users extends \Phalcon\Mvc\Model {
	public $Id;
	public $Username;
	public $Email;
	public $password;
	public $FirstName;
	public $LastName;
	public $ResumeUploaded;
	public $DateResumeUploaded;
	public $ResumeFile;
	
	public function isValid(){
		//Uniqueness
		$this->validate(new Uniqueness(array(
			'field' => 'Username',
			'message' => 'Your username is already taken'
		)));
		$this->validate(new Uniqueness(array(
			'field' => 'Email',
			'message' => 'Your email is already taken'
		)));
		
		//Length
		$this->validate(new StringLength(array(
			'field' => 'Username',
			'max' => 128,
			'min' => 1,
			'messageMaximum' => "Your username is too long",
			'messageMinimum' => "Your username is too short"		
		)));
		
		$this->validate(new StringLength(array(
			'field' => 'FirstName',
			'max' => 128,
			'messageMaximum' => "You must enter a valid first name (too long)",
		)));
		$this->validate(new StringLength(array(
			'field' => 'LastName',
			'max' => 128,
			'messageMaximum' => "You must enter a valid last name (too long)",
		)));
		
		$this->validate(new Email(array(
			'field' => 'Email',
			'message' => 'You must provide a valid E-Mail'
		)));
		
		if ($this->validationHasFailed() == true) {
			return false;
		}
	}
}
