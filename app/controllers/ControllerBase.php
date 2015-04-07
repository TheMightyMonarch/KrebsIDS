<?php

class ControllerBase extends \Phalcon\Mvc\Controller{
	public function afterExecuteRoute($dispatcher){
		if($this->request->isAjax()){
			$this->view->disable();
		}
    }
}
