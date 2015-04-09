<?php

class UsersController extends ControllerBase {
	//For keeping us safe from that nasty XSS
	private $filter;
	//We could use the flash component in lieu of this, but that's less flexible
	private $ajaxMessages = array();
	
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
			$this->response->redirect("users/settings");
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

				$this->response->redirect("users/settings");
			}
		}

		//Their login failed, no need to tell them why
		$this->flashSession->error("Login failed.");
	}

	/*
	 * Method allows for user registration
	 *
	 * We'd normally want some email confirmation and a captcha,
	 * but time constraints
	 */
	public function registerAction(){
		if(!$this->request->isPost()) return;

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
			$this->flashSession->error("Passwords must match.");
			
			return;
		}

		//Stores hash and salt in the same field for ease of use
		$user->Password = $this->security->hash($user->Password);

		if($user->save() !== false){
			//TODO add email verification

			$this->flashSession->success($user->FirstName . " " . $user->LastName . ", you are now registered. Please log in to view your settings page.");
			
			$this->response->redirect("users/login");
		}
		else{
			foreach($user->getMessages() as $message){
				$this->flashSession->error($message);
			}
		}
	}
	
	public function settingsAction(){
		$userId = $this->session->get('id');
	
		$user = Users::findFirstById($userId);

		$skills = Skills::find(array(
			'conditions' => 'UserId = :userId:',
			'bind' => array('userId' => $userId)
		));
		//$coverTemplate = CoverTemplates::fetchTemplateByUserId($userId);
		$aliases = CompanyAliases::find(array(
			'conditions' => 'UserId = :userId:',
			'bind' => array('userId' => $userId)
		));

		$this->view->setVar('resumeLocation', $user->ResumeFile);
		$this->view->setVar('skills', $skills);
		//$this->view->setVar('coverTemplate', $coverTemplate);		
		$this->view->setVar('aliases', $aliases);
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
				$username = preg_replace("/[^a-zA-Z0-9]/", "", $this->session->get("username"));
				$filename = $username . "." . $file->getExtension();
				
				$file->moveTo("/var/www/KrebsTracker/public/files/resumes/".$filename);
				
				//Update their account to reflect the existence of a resume
				$user = Users::findFirstById($this->session->get('id'));
				
				$user->ResumeUploaded = 1;
				$user->ResumeFile = $filename;
                                
                                $date = new DateTime();
				$user->DateResumeUploaded = $date->format('Y-m-d H:i:s');
				
				if(!$user->save()){
				
					foreach($user->getMessages() as $message){
						$this->flashSession->error($message->getMessage());
					}
				}
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
			echo "0";

			return;
		}
		
		$skill = $this->request->getPost('skill');
		$userId = $this->session->get('id');
		
		$skillModel = new Skills();
			
		$skillModel->UserId = $userId;
	
		//Security happens to be one of OUR skills; no XSS allowed
		$skillModel->Skill = $this->filter->sanitize($skill, 'string');

		if(!$skillModel->save()){
			echo "0";

			return;
		}

		echo $skillModel->Id;
	}
	
	public function ajaxAddCompanyAliasAction(){
		if(!$this->request->isPost() || !$this->request->isAjax()){
			echo "0";

			return;
		}
		
		$alias = $this->request->getPost('alias');
		$userId = $this->session->get('id');
			
		$aliasModel = new CompanyAliases();
			
		$aliasModel->UserId = $userId;
	
		//Security happens to be one of OUR skills; no XSS allowed
		$aliasModel->Alias = $this->filter->sanitize($alias, 'string');

		if(!$aliasModel->save()){
			echo "0";

			echo "0";
			
			return;
		}

		echo $aliasModel->Id;
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
	
	public function ajaxRemoveSkillAction(){
		if(!$this->request->isPost() || !$this->request->isAjax()){
			$this->ajaxMessages['errors'][] = "Invalid request.";
			echo json_encode($this->ajaxMessages);
			
			return;
		}
		
		$id = $this->request->getPost('id', 'string');
	
		$skills = new Skills();
		
		$skills->Id = $id;
		
		if($skills->delete()){
			$this->ajaxMessages['success'][] = "Removed row: ".$id;
		}
		else{
			$this->ajaxMessages['error'][] = "Failed to delete skill: ".$id;
		}
		
		echo json_encode($this->ajaxMessages);
	}
	
	public function ajaxRemoveCompanyAliasAction(){
		if(!$this->request->isPost() || !$this->request->isAjax()){
			$this->ajaxMessages['errors'][] = "Invalid request.";
			echo json_encode($this->ajaxMessages);
			
			return;
		}
		
		$id = $this->request->getPost('id', 'string');
	
		$alias = new CompanyAliases();
		
		$alias->Id = $id;
		
		if($alias->delete()){
			$this->ajaxMessages['success'][] = "Removed row: ".$id;
		}
		else{
			$this->ajaxMessages['error'][] = "Failed to delete skill: ".$id;
		}
		
		echo json_encode($this->ajaxMessages);
	}
	
	public function ajaxDeleteCoverTemplateAction(){
	}
}
