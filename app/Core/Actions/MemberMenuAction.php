<?php


namespace App\Core\Actions;


use App\Core\ApiManager;
use App\Core\CacheManager;
use App\Core\ReplyManager;

class MemberMenuAction implements IAction
{
	public function run($fbId)
	{
		// Generate member menus
		$replies = [
			ApiManager::makeQuickReply("My friend wants to join", "member_add"),
			ApiManager::makeQuickReply("Someone is leaving", "member_remove")
		];

		CacheManager::clear($fbId);
		CacheManager::storeCommand($fbId, "member_menu");
		ReplyManager::quickReply($fbId, "Anything i can do about peoples in '[current_event]'?", $replies);
	}

}