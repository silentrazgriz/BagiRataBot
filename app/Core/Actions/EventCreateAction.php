<?php
namespace App\Core\Actions;


use App\Core\CacheManager;
use App\Core\ReplyManager;
use App\Models\Event;

class EventCreateAction implements IAction
{
	public function run($fbId)
	{
		$cache = CacheManager::get($fbId);
		if ($cache->command == "event_create") {
			// Create new event from the name provided by user, maybe need confirmation..
			$chosenEvent = end($cache->messages);
			Event::create([
				"fbId" => $fbId,
				"name" => $chosenEvent,
				"members" => array(strtolower($cache->userProfile->first_name)),
				"transactions" => array()
			]);

			CacheManager::setCurrentEvent($fbId, $chosenEvent);
			CacheManager::clear($fbId);
			ReplyManager::reply($fbId, "Success, your current event is '[current_event]'");
		} else {
			// Ask user what name of your event
			CacheManager::storeCommand($fbId, "event_create");
			CacheManager::clearMessages($fbId);
			ReplyManager::reply($fbId, "Tell us what your event is called!");
		}
	}
}