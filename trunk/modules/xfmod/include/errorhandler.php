<?php


class XoopsForgeErrorHandler{
	var $messages;
	var $errors;
	var $system_error;

	function xoopsForgeErrorHandler(){
		$messages = array();
		$errors = array();
		$system_error = "";
	}

	function addMessage($info){
		$this->messages[] = $info;
	}

	function addError($info){
		$this->errors[] = $info;
	}

	function setSystemError($info){
		if(!headers_sent()){
			include(XOOPS_ROOT_PATH."/header.php");
			//OpenTable();
		}
		$content = "<p style='font-weight:bold; color:#FF0000; font-size:16pt'>"
				."System Error: "
				.$info
				."</p>";
		if(count($this->getFeedback())>0){
			$content .= "The following errors were found preceeding the system error and may have led to the problem:<BR>";
			$this->displayFeedback();
		}
		$content .= "<p>[ <a href='javascript:history.go(-1)'>Go Back</a> ]</p>";
		//CloseTable();
		include(XOOPS_ROOT_PATH."/footer.php");
		exit();
	}

	function getMessage(){
		$feedback = $this->messages;
		$this->messages = array();
		return $feedback;
	}

	function getError(){
		$feedback = $this->errors;
		$this->errors = array();
		return $feedback;
	}

	function getFeedback(){
		$feedback = array_merge($this->messages,$this->errors);
		$this->messages = array();
		$this->errors = array();
		return $feedback;
	}

	function getDisplayFeedback(){
		$content = '';
		if(count($this->messages)>0){
			$content .= "<div style='font-weight:bold;color:#0000DD'>"
						.implode("<br>",$this->messages)
						."</div>";
			$this->messages = array();
		}
		if(count($this->errors)>0){
			$content .= "<div style='font-weight:bold;color:#FF0000'>"
						.implode("<br>",$this->errors)
						."</div>";
			$this->errors = array();
		}
		return $content;
	}

	function displayFeedback(){
		echo $this->getDisplayFeedback();
	}
}

$xoopsForgeErrorHandler = new XoopsForgeErrorHandler();
?>