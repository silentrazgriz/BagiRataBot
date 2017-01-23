<?php


namespace App\Core\Actions;


use App\Core\CacheManager;
use App\Core\ReplyManager;

class CancelAction implements IAction
{
	public function run($fbId)
	{
		// Clear cache (not the current event) and run main menu again
		CacheManager::clear($fbId);
		ReplyManager::reply($fbId, "Don't worry, let's try again!");

		$mainMenu = new MainMenuAction();
		$mainMenu->run($fbId);
	}
}