<?php
namespace App\Core\Actions;


use App\Core\Actions\Structures\IAction;
use App\Core\CacheManager;
use App\Core\ReplyManager;
use App\Models\Event;

class EventDeleteAction implements IAction
{
	public function message($fbId)
	{
		// Delete chosen event
		$cache = CacheManager::get($fbId);
		$chosenEvent = $cache->messages[count($cache->messages) - 1];
		Event::where("fbId", $fbId)->where("name", $chosenEvent)->delete();

		CacheManager::clear($fbId);
		ReplyManager::reply($fbId, "'$chosenEvent' successfully removed!");

		$mainMenu = new MainMenuAction();
		$mainMenu->postback($fbId);
	}

	public function quickReply($fbId)
	{
		// Ask user what name of your event
		CacheManager::clearMessages($fbId);
		ReplyManager::reply($fbId, "Tell us which event you don't need?");
	}

	public function postback($fbId)
	{
		// TODO: Implement postback() method.
	}


}