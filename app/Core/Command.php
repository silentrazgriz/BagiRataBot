<?php
namespace App\Core\BagiRata;

use App\Core\BagiRata\Actions\Events;

class Command
{
	public $name, $instance, $function, $needActiveEvent, $argumentsNumber, $errorArgumentsMessage, $allowMultipleValues;

	public function __construct($name, $instance, $function, $needActiveEvent, $argumentsNumber, $errorArgumentsMessage = '', $allowMultipleValues = false) {
		$this->name = $name;
		$this->instance = $instance;
		$this->function = $function;
		$this->needActiveEvent = $needActiveEvent;
		$this->allowMultipleValues = $allowMultipleValues;
		$this->argumentsNumber = $argumentsNumber;
		$this->errorArgumentsMessage = $errorArgumentsMessage;
 	}

	public function run($fbId, array $arguments) {
		if (count($arguments) < $this->argumentsNumber) {
			SendMessage::callApi($fbId, $this->errorArgumentsMessage." belum disertakan, mohon coba lagi");
		} else {
			$obj = new $this->instance;
			call_user_func_array(array($obj, $this->function), array($fbId, $arguments));
		}
	}

	public function isCommand($keyword) {
		return $keyword == $this->name;
	}

	public function checkActiveEvent($fbId) {
		if ($this->needActiveEvent && !Events::getActiveEvent($fbId)) {
			return false;
		}
		return true;
	}
}