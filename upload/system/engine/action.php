<?php
// *	@copyright	OCSHOP.CMS \ ocshop.net 2011 - 2015.
// *	@demo	http://ocshop.net
// *	@blog	http://ocshop.info
// *	@forum	http://forum.ocshop.info
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class Action {
	private $file;
	private $class;
	private $method;
	private $args = array();

	public function __construct($route, $args = array()) {
		$parts = explode('/', str_replace('../', '', (string)$route));

		// Break apart the route
		while ($parts) {
			$file = DIR_APPLICATION . 'controller/' . implode('/', $parts) . '.php';

			if (is_file($file)) {
				$this->file = $file;

				$this->class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', implode('/', $parts));
				break;
			} else {
				$this->method = array_pop($parts);
			}
		}

		if (!$this->method) {
			$this->method = 'index';
		}
		
		$this->args = $args;
	}

	public function execute($registry, $args = array()) {
		// Stop any magical methods being called
		if (substr($this->method, 0, 2) == '__') {
			return false;
		}

		if (is_file($this->file)) {
			include_once($this->file);

			$class = $this->class;

			$controller = new $class($registry);

			if (is_callable(array($controller, $this->method))) {
				return call_user_func(array($controller, $this->method), $args);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	public function getArgs() {
		return $this->args;
	}	
}
