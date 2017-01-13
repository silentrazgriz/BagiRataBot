<?php
namespace App\Core;

use App\Core\Actions\EventChooseAction;
use App\Core\Actions\EventCreateAction;
use App\Core\Actions\HelpAction;
use App\Core\Actions\MenuAction;

class BotManager
{
	private $commands = [];

	public function __construct()
	{
		$this->commands = [
			new Command('help', HelpAction::class),
			new Command('main_menu', MenuAction::class),
			new Command('event_create', EventCreateAction::class),
			new Command('event_choose', EventChooseAction::class)
		];
	}

	public function receivedMessage($message) {
		$fbId = $message->sender->id;
		echo 'hi';
		if (isset($message->attachments)) {
			ApiManager::sendMessage($fbId, "Attachment not available yet");
			return;
		} else {
			if (isset($message->message)) {
				$cache = CacheManager::get($fbId);
				CacheManager::storeMessages($fbId, $message->message->text);
				$this->getAction($cache->command)->invoke($fbId);
			} else if (isset($message->postback)) {
				$this->getAction($message->postback->payload)->invoke($fbId);
			}
		}
	}

	public function getAction($payload) {
		foreach($this->commands as $command) {
			if ($command->isCommand($payload)) {
				return $command;
			}
		}
		return null;
	}
}