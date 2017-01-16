<?php
namespace App\Core\Actions;

use App\Core\ReplyManager;

class HelpAction implements IAction
{
	public function run($fbId)
	{
		ReplyManager::reply($fbId, "Hi [first_name]. We can help you split your bills with your friends.\n\n" .
			"Want to start right away? Open menu and choose 'Let's start'\n\n" .
			"Make a mistake in the middle of your data input? Open menu and choose 'I think i made a mistake'");
	}
}