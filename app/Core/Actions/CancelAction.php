<?php


namespace App\Core\Actions;


use App\Core\Actions\Structures\IAction;
use App\Core\CacheManager;
use App\Core\ReplyManager;

class CancelAction implements IAction
{

	public function message($fbId)
	{
		// TODO: Implement message() method.
	}

	public function quickReply($fbId)
	{
		// TODO: Implement quickReply() method.
	}

	public function postback($fbId)
	{
		// Clear cache (not the current event) and run main menu again
		CacheManager::clear($fbId);
		ReplyManager::reply($fbId, "Don't worry, let's try again!");

		$mainMenu = new MainMenuAction();
		$mainMenu->postback($fbId);
	}

}