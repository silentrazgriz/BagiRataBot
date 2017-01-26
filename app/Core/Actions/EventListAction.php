<?php


namespace App\Core\Actions;


use App\Core\Actions\Structures\IAction;
use App\Core\CacheManager;
use App\Core\ReplyManager;
use App\Models\Event;

class EventListAction implements IAction
{
	public function message($fbId)
	{
		// TODO: Implement message() method.
	}

	public function quickReply($fbId)
	{
		$events = Event::where("fbId", $fbId)->get();
		$reply = "Here's your event lists:\n";
		foreach($events as $key => $event) {
			$reply .= ($key+1) . ". $event->name\n";
		}

		CacheManager::clear($fbId);
		ReplyManager::reply($fbId, $reply);

		$mainMenu = new MainMenuAction();
		$mainMenu->postback($fbId);
	}

	public function postback($fbId)
	{
		// TODO: Implement postback() method.
	}


}