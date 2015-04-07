<?php

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\File,
    Phalcon\Forms\Element\Submit;
    
class Forms_ResumeForm extends Form {
	public function generate(){
		//There's only one field here, but for convention we'll leave it be
		$fields = array(
			'resume' => new File("resume", array(
				'class' => 'form-control'
			)
		));
		
		$fields['resume']->setLabel("Upload Resume");
		
		//TODO add validators for file input
		
		foreach($fields as $field){
			$this->add($field);
		}
		
		$this->add(new Submit('upload', array(
			'value' => 'Upload',
			'class' => 'form-control btn btn-primary'
		)));
	}
}
