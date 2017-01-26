<?php


namespace App\Core\Actions;


use App\Core\Actions\Structures\IAction;
use App\Core\ApiManager;
use App\Core\CacheManager;
use App\Core\ReplyManager;

class TransactionMenuAction implements IAction
{
	public function message($fbId)
	{
		$this->quickReply($fbId);
	}

	public function quickReply($fbId)
	{
		// Generate transaction menus
		$replies = [
				ApiManager::makeQuickReply("I have a new transaction", "transaction_create"),
				ApiManager::makeQuickReply("I want to remove a transaction", "transaction_delete")
		];

		CacheManager::clear($fbId);
		ReplyManager::quickReply($fbId, "Tell us what you need!", $replies);
	}

	public function postback($fbId)
	{
		// TODO: Implement postback() method.
	}

}