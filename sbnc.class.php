<?php
class sbnc {

	const PREFIX = 'sbnc_';
	const BOT = 'info';
	const TIME = 'time';
	const KEY = 'key';
	const MOUSE = 'mouse';
	const JS = 'js';
	const VERSION = '0.1.4pre';

	private $strict;
	private $html5;
	private $min;
	private $max;
	private $nojs;
	private $checkmail;
	private $gestmode;
	private $req = array();
	public $errors = array();

	public function __construct($strict = true, $html5 = true, $min = 2, $max = 1800) {
		$this->strict = $strict;
		$this->html5 = $html5;
		$this->min = $min;
		$this->max = $max;
		$this->option_checkmail();
		$this->option_nojs();
		$this->option_gestmode();
	}

	public function option_strict($strict = true) {
		$this->strict = $strict;
	}

	public function option_html5($html5 = true) {
		$this->html5 = $html5;
	}

	public function option_min($min = 2) {
		$this->min = $min;
	}

	public function option_max($max = 1800) {
		$this->max = $max;
	}

	public function option_checkmail($check = true, $required = true) {
		$this->checkmail = array($check, $required);
	}

	public function option_nojs($nojs = false) {
		$this->nojs = $nojs;
		if($nojs) {
			$this->strict = false;
		}
	}

	public function option_gestmode($gestmode = 'mouse') {
		switch ($gestmode) {
			case 'both':
				$this->gestmode = 0;
				break;
			case 'mouse':
				$this->gestmode = 1;
				break;
			case 'keyboard':
				$this->gestmode = 2;
				break;
			default:
				$this->gestmode = 1;
				break;
		}
	}

	public function checkRequest() {
	    if ($this->submit()) {
    		$this->req = $_POST;
    		$this->isUser();
    		$this->isValid();
    		return empty($this->errors);
	    }
	    return false;
	}

	public function submit() {
		return ($_SERVER['REQUEST_METHOD'] == 'POST');
	}

	private function isUser() {
		if($this->checkStrict()) {
			$this->checkHidden();
			$this->checkTime();
			if(!$this->strict && $this->checkJs()) {
				$this->checkGestures();
			} else if($this->strict) {
				$this->checkGestures();
			}
		}
	}

	private function checkHidden() {
		if(!$this->isEmpty(self::PREFIX.self::BOT)) {
			$this->addError(10);
		}
	}

	private function checkTime() {
		if(!is_numeric($this->getPost(self::PREFIX.self::TIME))) {
			$this->addError(45);
		}
		if(time() - $this->getPost(self::PREFIX.self::TIME) < $this->min) {
			$this->addError(21);
		}
		if(time() - $this->getPost(self::PREFIX.self::TIME) > $this->max) {
			$this->addError(22);
		}
	}

	private function checkGestures() {
		if ($this->gestmode === 0 || $this->gestmode === 1) {
			if(!$this->isEmpty(self::PREFIX.self::MOUSE) && strcmp($this->getPost(self::PREFIX.self::MOUSE), 'true') != 0) {
				$this->addError(41);
			}
		}
		if ($this->gestmode === 0 || $this->gestmode === 2) {
			if(!$this->isEmpty(self::PREFIX.self::KEY) && strcmp($this->getPost(self::PREFIX.self::KEY), 'true') != 0) {
				$this->addError(42);
			}
		}
	    	if($this->gestmode === 0 && $this->isEmpty(self::PREFIX.self::MOUSE) && $this->isEmpty(self::PREFIX.self::KEY)) {
	    		$this->addError(30);
	    	} else {
	    		if($this->gestmode === 1 &&$this->isEmpty(self::PREFIX.self::MOUSE)) {
	        		$this->addError(31);
	        	}
	        	if($this->gestmode === 2 && $this->isEmpty(self::PREFIX.self::KEY)) {
	        		$this->addError(32);
	        	}
	    	}
	}

	private function checkStrict() {
		if($this->strict && ($this->isEmpty(self::PREFIX.self::JS) || strcmp($this->getPost(self::PREFIX.self::JS), 'true') != 0)) {
			$this->addError(70);
			return false;
		}
		return true;
	}

	private function checkJs() {
		$re = true;
		if(!$this->isEmpty(self::PREFIX.self::JS) && strcmp($this->getPost(self::PREFIX.self::JS), 'true') != 0) {
			$this->addError(43);
			$re = false;
		}
		if($this->isEmpty(self::PREFIX.self::JS) && (!$this->isEmpty(self::PREFIX.self::MOUSE) || !$this->isEmpty(self::PREFIX.self::KEY))) {
			$this->addError(44);
			$re = false;
		}
		if($this->isEmpty(self::PREFIX.self::JS) || strcmp($this->getPost(self::PREFIX.self::JS), 'false') == 0) {
			$re = false;
		}
		return $re;
	}

	private function isValid() {
		foreach($this->req as $key => $value) {
			if($this->checkmail[0] && (strcmp($key, 'email') == 0 || strcmp($key, 'mail') == 0)) {
				if(($this->checkmail[1]) || (!$this->checkmail[1] && !$this->isEmpty($key))) {
					if(!$this->validEmail($key)) {
						$this->addError(60);
					}
				}
			}
			if(strcmp($value, strip_tags($value)) != 0) {
				$this->addError(50);
			}
		}
	}

	private function isEmpty($key) {
		return (strlen(trim($this->getPost($key))) == 0);
	}

	private function getPost($key) {
		return isset($this->req[$key]) ? $this->req[$key] : '';
	}

	private function validEmail($key) {

		return filter_var($this->getPost($key), FILTER_VALIDATE_EMAIL);
	}

	public function filter($key, $nl2br = false) {
		if($nl2br) {
			return !empty($_POST[$key]) ? nl2br(htmlspecialchars($_POST[$key], ENT_QUOTES)) : '';
		} else {
			return !empty($_POST[$key]) ? htmlspecialchars($_POST[$key], ENT_QUOTES) : '';
		}
	}

	public function addError($msg) {
		array_push($this->errors, $msg);
	}

	public function getErrorMessages() {
		$list = array();
			foreach($this->errors as $error) {
				if(is_numeric($error)) {
					array_push($list, $this->getError($error));
				} else {
					array_push($list, $error);
				}
			}
		return $list;
	}

	public function getError($code) {
		$msg;
		switch($code) {
			case 10:
				$msg = 'Hidden field '.self::PREFIX.self::BOT.' not empty';
				break;
			case 21:
				$msg = 'Form submitted too fast';
				break;
			case 22:
				$msg = 'Form submit too late';
				break;
			case 30:
				$msg = 'No keypress and mouse movement';
				break;
			case 31:
				$msg = 'Mouse not moved';
				break;
			case 32:
				$msg = 'Keyboard not used';
				break;
			case 41:
				$msg = 'Hidden field '.self::PREFIX.self::MOUSE.' manipulated';
				break;
			case 42:
				$msg = 'Hidden field '.self::PREFIX.self::KEY.' manipulated';
				break;
			case 43:
				$msg = 'Hidden field '.self::PREFIX.self::JS.' manipulated';
				break;
			case 44:
				$msg = 'Field '.self::PREFIX.self::MOUSE.'/'.self::PREFIX.self::KEY.' manupulated';
				break;
			case 45:
				$msg = 'Timestamp not numeric';
				break;
			case 50:
				$msg = 'Field contains HTML-Tags';
				break;
			case 60:
				$msg = 'No valid email';
				break;
			case 70:
				$msg = 'In strict mode javascript must be enabled';
				break;
			default:
				$msg = $code;
		}
		return $msg;
	}

	public function novalidate() {
		if($this->html5) {
			echo ' novalidate';
		}
	}

	public function printHead($jquery = true) {
	    $lang = !$this->html5 ? ' language="javascript" type="text/javascript"' : '';
		if(!$this->nojs) {
			echo "<script".$lang.">\n";
			echo "function sbncinit() {\n";
			echo "window.onkeyup = keyup;\n";
			echo "window.onmousemove = mousemove;\n";
			echo "document.getElementById(\"".self::PREFIX.self::JS."\").value = 'true';\n";
			echo "}\n";
			echo "function keyup() {\n";
			echo "document.getElementById(\"".self::PREFIX.self::KEY."\").value = 'true';\n";
			echo "}\n";
			echo "function mousemove() {\n";
			echo "document.getElementById(\"".self::PREFIX.self::MOUSE."\").value = 'true';\n";
			echo "}\n";
			echo "document.addEventListener('readystatechange', function() {\n";
			echo "if (document.readyState === \"complete\") {\n";
			echo "sbncinit();\n";
			echo "}\n";
			echo "});\n";
			echo "</script>\n";
		}
	}

	public function printFields($html5 = true) {
		$tagend = ($this->html5 && $html5) ? 'required' : '/';

		echo "<input type=\"text\" id=\"sbnc_js\" name=\"".self::PREFIX.self::JS."\" style=\"display:none\" $tagend>\n";
		echo "<input type=\"text\" id=\"sbnc_info\" name=\"".self::PREFIX.self::BOT."\" style=\"display:none\" $tagend>\n";
		echo "<input type=\"text\" id=\"sbnc_time\" name=\"".self::PREFIX.self::TIME."\" value=\"".time()."\" style=\"display:none\" $tagend>\n";
		echo "<input type=\"text\" id=\"sbnc_mouse\" name=\"".self::PREFIX.self::MOUSE."\" style=\"display:none\" $tagend>\n";
		echo "<input type=\"text\" id=\"sbnc_key\" name=\"".self::PREFIX.self::KEY."\" style=\"display:none\" $tagend>\n";
	}

}
