<?php

use Phalcon\Forms\Form,
	Phalcon\Forms\Element\Text,
	Phalcon\Forms\Element\Password,
	Phalcon\Forms\Element\Select,
	Phalcon\Forms\Element\Submit,
	Phalcon\Validation\Validator\PresenceOf,
	Phalcon\Validation\Validator\Email,
	Phalcon\Validation\Validator\Confirmation,
	Phalcon\Validation\Validator\StringLength;

class Forms_RegisterForm extends Form {
	public function generate(){
		$fields = array(
			'firstName' => new Text('firstName', array(
				'maxLength' => 30,
				'placeholder' => 'Enter your first name',
				'class' => 'form-control'
			)),
			'lastName' => new Text('lastName', array(
				'maxLength' => 30,
				'placeholder' => 'Enter your last name',
				'class' => 'form-control'
			)),
			'username' => new Text('username', array(
				'maxLength' => 30,
				'placeholder' => 'Enter a username',
				'class' => 'form-control'
			)),
			'email' => new Text('email', array(
				'maxLength' => 30,
				'placeholder' => 'Enter your email',
				'class' => 'form-control'
			)),
			'password' => new Password('password', array(
				'maxLength' => 30,
				'placeholder' => 'Enter a password',
				'class' => 'form-control'
			)),
			'passwordConfirm' => new Password('passwordConfirm', array(
				'maxLength' => 30,
				'placeholder' => 'Re-enter password',
				'class' => 'form-control'
			))
		);

		//Labeling
		$fields['firstName']->setLabel('First Name');
		$fields['lastName']->setLabel('Last Name');
		$fields['username']->setLabel('Username');
		$fields['email']->setLabel('E-Mail');
		$fields['password']->setLabel('Password');
		$fields['passwordConfirm']->setLabel('Confirm Password');
		
		//First name
		$fields['firstName']->addValidator(new StringLength(array(
			'max' => 30,
			'maxMessage' => 'Invalid first name (too long)'
		)));
		$fields['firstName']->addValidator(new PresenceOf(array(
			'message' => 'First name is a required field'
		)));
		
		//Last Name
		$fields['lastName']->addValidator(new StringLength(array(
			'max' => 30,
			'maxMessage' => 'Invalid last name (too long)'
		)));
		$fields['lastName']->addValidator(new PresenceOf(array(
			'message' => 'Last name is a required field'
		)));
		
		//E-mail address
		$fields['email']->addValidator(new StringLength(array(
			'max' => 30,
			'maxMessage' => 'Invalid email (too long)'
		)));
		$fields['email']->addValidator(new Email(array(
			'message' => 'Invalid email (bad format)'
		)));
		$fields['email']->addValidator(new PresenceOf(array(
			'message' => 'E-Mail is a required field'
		)));
		
		//Password
		$fields['password']->addValidator(new StringLength(array(
			'max' => 30,
			'min' => 8,
			'messageMaximum' => 'Your password must not exceed 30 characters',
			'messageMinimum' => 'Your password must be at least 8 characters'
		)));
		$fields['passwordConfirm']->addValidator(new Confirmation(array(
			'with' => 'pass',
			'message' => 'Passwords do not match'
		)));
		$fields['password']->addValidator(new PresenceOf(array(
			'message' => 'Password is a required field'
		)));
		$fields['passwordConfirm']->addValidator(new PresenceOf(array(
			'message' => 'You must re-enter your password'
		)));
		
		foreach($fields as $field){
			$this->add($field);
		}
		
		$this->add(new Submit('submit', array(
			'value' => 'Register',
			'class' => 'form-control btn btn-primary'
		)));
	}
}
