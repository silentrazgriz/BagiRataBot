<?php
namespace App\Core\Actions;


use App\Core\Actions\Structures\IAction;
use App\Core\CacheManager;
use App\Core\ReplyManager;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class EventCreateAction implements IAction
{
	public function message($fbId)
	{
		$cache = CacheManager::get($fbId);
		// Create new event from the name provided by user, maybe need confirmation..
		$chosenEvent = $cache->messages[count($cache->messages) - 1];
		DB::transaction(function() use ($fbId, $chosenEvent, $cache) {
			Event::create([
					"fbId" => $fbId,
					"name" => $chosenEvent,
					"members" => array(strtolower($cache->userProfile->first_name)),
					"transactions" => array()
			]);
		});
		CacheManager::setCurrentEvent($fbId, $chosenEvent);
		CacheManager::clear($fbId);
		ReplyManager::reply($fbId, "Success, your current event is '[current_event]'");
		$eventMenu = new EventMenuAction();
		$eventMenu->quickReply($fbId);
	}

	public function quickReply($fbId)
	{
		// Ask user what name of your event
		CacheManager::clear($fbId);
		ReplyManager::reply($fbId, "Tell us what your event is called!");
	}

	public function postback($fbId)
	{
		// TODO: Implement postback() method.
	}


}