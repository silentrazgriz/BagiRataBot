<?php
namespace App\Core;

use App\Core\Actions\IAction;

class Command
{
	/*
	 * Temporary values
	 *
	 *
	 */
	public $name, $action, $numberOfArguments;

	public function __construct($name, IAction $action) {
		$this->name = $name;
		$this->action = $action;
 	}

	public function invoke($fbId) {
		$this->action->run($fbId);
	}

	public function isCommand($keyword) {
		return $keyword == $this->name;
	}
}