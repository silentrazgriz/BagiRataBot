<?php


namespace App\Core\Actions;


use App\Core\Actions\Structures\IAction;
use App\Core\ApiManager;
use App\Core\CacheManager;
use App\Core\ReplyManager;

class MemberMenuAction implements IAction
{
	public function message($fbId)
	{
		$this->quickReply($fbId);
	}

	public function quickReply($fbId)
	{
		// Generate member menus
		$replies = [
				ApiManager::makeQuickReply("My friend wants to join", "member_add"),
				ApiManager::makeQuickReply("Someone is leaving", "member_remove"),
				ApiManager::makeQuickReply("Nevermind", "event_menu"),
		];

		CacheManager::clear($fbId);
		ReplyManager::quickReply($fbId, "Anything i can do about people in '[current_event]'?", $replies);
	}

	public function postback($fbId)
	{
		// TODO: Implement postback() method.
	}

}