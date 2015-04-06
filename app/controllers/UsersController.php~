<?php

class UsersController extends ControllerBase {
	//For keeping us safe from that nasty XSS
	private $filter;
	//We could use the flash component in lieu of this, but that's less flexible
	private $ajaxMessages;
	
	//if this were php 5.6 we could make this a real constant
	private $MIME_WHITELIST = array(
		'pdf'	=> 'application/pdf',
		'docx'	=> 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'odt'	=> 'application/vnd.oasis.opendocument.text'
	);
	const MAX_FILESIZE_BYTE = 8192000; //8 MB

	public function initialize(){
		$this->filter = new \Phalcon\Filter();
	}

	//Login business logic
	public function loginAction(){
		if($this->session->get('id')){
			$this->dispatcher->forward(array(
				'controller'	=> 'users',
				'action'		=> 'settings'
			));
		}
	
		if(!$this->request->isPost() || $this->request->getPost('registered') == true) return;

		$username = $this->request->getPost('username');
		$password = $this->request->getPost('password');

		//magic method for doing exactly what it says it's doing
		$user = Users::findFirstByUsername($username);

		if($user){
			//The salt is included within the hash automatically
			if($this->security->checkHash($password, $user->Password)){
				$this->session->set('id', $user->Id);
				$this->session->set('username', $username);

				//Clear out data we don't want people seeing
				unset($_POST['password']);

				$this->dispatcher->forward(array(
					'controller'	=> 'users',
					'action'		=> 'settings'
				));
			}
		}

		//Their login failed, no need to tell them why
		$messages[] = "Login failed.";
		
		$this->view->setVar('messages', $messages);
	}

	/*
	 * Method allows for user registration
	 *
	 * We'd normally want some email confirmation and a captcha,
	 * but time constraints
	 */
	public function registerAction(){
		if(!$this->request->isPost()) return;

		$messages = array();

		$user = new Users();

		$user->Username = $this->request->getPost('username', 'string');
		$user->Password = $this->request->getPost('password', 'string');
		$passwordConfirm = $this->request->getPost('passwordConfirm', 'string');
		$user->Email = $this->request->getPost('email', 'string');
		$user->FirstName = $this->request->getPost('firstName', 'string');
		$user->LastName = $this->request->getPost('lastName', 'string');
		
		//unused fields during registration
		$user->ResumeUploaded = 0;
		$user->DateResumeUploaded = "0000-00-00 00:00:00";
		$user->ResumeFile = "none";

		if($user->Password !== $passwordConfirm){
			$messages[] = "Passwords must match";
			$this->view->setVar('messages', $messages);
			return;
		}

		//Stores hash and salt in the same field for ease of use
		$user->Password = $this->security->hash($user->Password);

		if($user->save() !== false){
			//TODO add email verification

			$messages[] = "User account successfully created!";
	
			//Forwarding keeps our request object, so we'll want to clear the password
			$_POST['password'] = '';
			$_POST['passwordConfirm'] = '';
	
			$this->dispatcher->forward(array(
				'controller'	=> 'users',
				'action'		=> 'login',
				'params'		=> array('registered' => true)
			));
		}
		else{
			foreach($user->getMessages() as $message){
				$messages[] = $message->getMessage();
			}
		}
		
		$this->view->setVar('messages', $messages);
	}
	
	public function settingsAction(){
		$userId = $this->session->get('id');
	
		$user = Users::findFirstById($userId);

		$skills = Skills::fetchSkillsByUserId($userId);
		//$coverTemplate = CoverTemplates::fetchTemplateByUserId($userId);
		//$companyAliases = CompanyAliases::fetchCompanyAliasesByUserId($userId);
		
		$this->view->setVar('resumeLocation', $user->ResumeFile);
		$this->view->setVar('skills', $skills);
		//$this->view->setVar('coverTemplate', $coverTemplate);		
		//$this->view->setVar('companyAliases', $companyAliases);
	}
	
	/*
	 * Helper actions
	 */
	
	//Create actions
	
	public function uploadResumeAction(){
		if(!$this->request->hasFiles()){
			$this->flashSession->error('File upload error/No files provided');
			
			$this->response->redirect("users/settings");
			
			return;
		}
		
		foreach($this->request->getUploadedFiles() as $file){
			//Check to see if the extension and (real) MIME type are allowed and match
			if(array_key_exists($file->getExtension(), $this->MIME_WHITELIST)
				&& in_array($file->getRealType(), $this->MIME_WHITELIST)
			){
				if($file->getSize() > self::MAX_FILESIZE_BYTE){
					$this->flashSession->error('File too large: ' . ($file->getSize() / 1000000) . "M.");
					break;
				}
				
				//We can just replace their old resume
				//TODO add mutliple resume support
				
				//make our filename safe
				$username = preg_replace("/^[a-zA-Z0-9]/", "", $this->session->get("username"));
				$filename = $username . "." . $file->getExtension();
				
				$file->moveTo("/var/www/KrebsTracker/public/files/resumes/".$filename);
				
				//Update their account to reflect the existence of a resume
				$user = Users::findFirstById($this->session->get('id'));
				
				$user->ResumeUploaded = true;
				$user->ResumeFile = $filename;
				$user->DateResumeUploaded = (new DateTime())->format('Y-m-d H:i:s');
				
				$user->save();
				
				$this->flashSession->success("Successfully uploaded resume.");
			}
			else{
				$this->flashSession->error("Invalid file type.");
				break;
			}
		}
		
		$this->response->redirect("users/settings");
	}
	
	public function ajaxAddSkillAction(){
		if(!$this->request->isPost() || !$this->request->isAjax()){
			$this->ajaxMessages['errors'][] = "No skills selected";
			echo json_encode($this->ajaxMessages);

			return;
		}
		
		$skills = $this->request->getPost('skills');
		$userId = $this->session->get('id');
		
		$skillsModel = new Skills();

		//Gives the front end some flexibility
		if(!is_array($skills)){
			$skills = array($skills);
		}
		
		foreach($skills as $skill){
			$skillModel = new Skills();
			
			$skillModel->UserId = $userId;
		
			//Security happens to be one of OUR skills; no XSS allowed
			$skillsModel->Skill = $this->filter->sanitize($skill, 'string');

			if(!$skill->save()){
				$this->ajaxMessages['errors'][] = "Failed to save skill: ".$skill->Skill;
				break;
			}
			$this->ajaxMessages['success'][] = "Successfully saved skill: ".$skill->Skill;
		}
		
		echo json_encode($this->ajaxMessages);
	}
	
	public function ajaxAddCompanyAliasAction(){
		if(!$this->request->isPost() || !$this->request->isAjax()){
			$this->ajaxMessages['errors'][] = "No company aliases selected";
			echo json_encode($this->ajaxMessages);

			return;
		}
		
		$aliases = $this->request->getPost('aliases');
		$userId = $this->session->get('id');
		
		$aliasRows = array();
		
		//Again, front end flexibility
		if(!is_array($aliases)){
			$aliases = array($aliases);
		}
		
		foreach($aliases as $alias){
			$aliasModel = new CompanyAliases();
			
			$aliasModel->UserId = $userId;
		
			//Security happens to be one of OUR skills; no XSS allowed
			$aliasesModel->Skill = $this->filter->sanitize($alias, 'string');

			if(!$aliasesModel->isValid()){
				$this->ajaxMessages['errors'][] = "Skill: \"$alias\" is invalid";
			}
			else{
				$aliasRows[] = $aliasesModel;
			}
		}
		
		if(empty($this->ajaxMessages['errors'])){
			foreach($aliasRows as $row){
				$row->save();
			}
		}
		
		echo json_encode($this->ajaxMessages);
	}
	
	public function ajaxAddCoverTemplateAction(){
		if(!$this->request->isPost() || !$this->request->isAjax()){
			$this->ajaxMessages['errors'][] = "No template provided";
			echo json_encode($this->ajaxMessages);
			
			return;
		}
		
		$template = $this->request->getPost('coverTemplate', 'string');
		
	}
	
	//Delete actions
	
	//This isn't really necessary right now since resumes are overwritten when a new one is uploaded
	public function deleteResumeAction(){
	}
	
	public function ajaxDeleteSkillAction(){
		if(!$this->request->isPost() || !$this->request->isAjax()){
			$this->ajaxMessages['errors'][] = "Invalid request.";
			echo json_encode $this->ajaxMessages;
			
			return;
		}
		
		$id = $this->request->getPost('skillId', 'string');
		
		$skills = new Skills();
		
		$skills->Id = $id;
		
		if($skils->delete()){
			$this->ajaxMessages['success'][] = "Removed row: ".$id;
		}
		else{
			$this->ajaxMessages['error'][] = "Failed to delete skill: ".$id;
		}
		
		echo json_encode($this->ajaxMessages);
	}
	
	public function ajaxDeleteCompanyAliasAction(){
	}
	
	public function ajaxDeleteCoverTemplateAction(){
	}
}
