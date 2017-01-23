<?php
namespace App\Core\Actions;


use App\Core\CacheManager;
use App\Core\ReplyManager;
use App\Models\Event;

class EventDeleteAction implements IAction
{
	public function run($fbId)
	{
		$cache = CacheManager::get($fbId);
		if ($cache->command == "event_delete") {
			// Delete chosen event
			$chosenEvent = $cache->messages[count($cache->messages) - 1];
			Event::where("fbId", $fbId)->where("name", $chosenEvent)->delete();

			CacheManager::clear($fbId);
			ReplyManager::reply($fbId, "'$chosenEvent' successfully removed!");
		} else {
			// Ask user what name of your event
			CacheManager::clearMessages($fbId);
			CacheManager::storeCommand($fbId, "event_delete");
			ReplyManager::reply($fbId, "Tell us which event you don't need?");
		}
	}
}