<?php

use Phalcon\Forms\Form,
	Phalcon\Forms\Element\Text,
	Phalcon\Forms\Element\Password,
	Phalcon\Forms\Element\Select,
	Phalcon\Forms\Element\Submit,
	Phalcon\Validation\Validator\PresenceOf,
	Phalcon\Validation\Validator\StringLength;

class Forms_LoginForm extends Form {
	public function generate(){
		$username = new Text("username", array(
			'maxLength'	=> 30,
			'placeholder'	=> 'Type your username',
			'class' => 'form-control'
		));
		
		$username->setLabel('Username');

		$username->addValidator(new PresenceOf(array(
			'message' => "You must enter your username.",
		)));
		
		$pass = new Password('password', array(
			'maxLength'	=> 30,
			'placeholder'	=> 'Enter your password',
			'class' 		=> 'form-control'
		));
		
		$pass->setLabel('Password');

		$pass->addValidator(new PresenceOf(array(
			'message' => "You must enter your password."
		)));
		
		$pass->addValidator(new StringLength(array(
			'min' => 6,
			'messageMinimum' => 'Your password must be at least 6 characters long.'
		)));

		$this->add($username);
		$this->add($pass);
		
		$this->add(new Submit('submit', array(
			'value' => 'Log In!',
			'class' => 'form-control btn btn-primary'
		)));
	}
}

