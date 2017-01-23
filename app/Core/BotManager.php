<?php
namespace App\Core;

use App\Core\Actions\CancelAction;
use App\Core\Actions\EventChooseAction;
use App\Core\Actions\EventCreateAction;
use App\Core\Actions\EventDeleteAction;
use App\Core\Actions\EventDetailAction;
use App\Core\Actions\EventListAction;
use App\Core\Actions\EventMenuAction;
use App\Core\Actions\EventSummaryAction;
use App\Core\Actions\HelpAction;
use App\Core\Actions\MainMenuAction;
use App\Core\Actions\MemberAddAction;
use App\Core\Actions\MemberMenuAction;
use App\Core\Actions\MemberRemoveAction;
use App\Core\Actions\TransactionCreateAction;
use App\Core\Actions\TransactionDeleteAction;
use App\Core\Actions\TransactionMenuAction;
use Illuminate\Support\Facades\Log;

class BotManager
{
	private $commands = [];

	public function __construct()
	{
		$this->commands = [
			new Command("cancel", new CancelAction()),
			new Command("event_choose", new EventChooseAction()),
			new Command("event_create", new EventCreateAction()),
			new Command("event_delete", new EventDeleteAction()),
			new Command("event_detail", new EventDetailAction()),
			new Command("event_list", new EventListAction()),
			new Command("event_menu", new EventMenuAction()),
			new Command("event_summary", new EventSummaryAction()),
			new Command("help", new HelpAction()),
			new Command("main_menu", new MainMenuAction()),
			new Command("member_add", new MemberAddAction()),
			new Command("member_add_another", new MemberAddAction()),
			new Command("member_menu", new MemberMenuAction()),
			new Command("member_remove", new MemberRemoveAction()),
			new Command("transaction_create", new TransactionCreateAction()),
			new Command("transaction_delete", new TransactionDeleteAction()),
			new Command("transaction_menu", new TransactionMenuAction())
		];
	}

	public function receiveMessage($data) {
		$fbId = $data->sender->id;
		$cache = CacheManager::get($fbId);

		CacheManager::storeMessages($fbId, $data->message->text);
		Log::info("Invoking command in message '" . $cache->command . "'");
		$this->getAction($cache->command)->invoke($fbId);
	}

	public function receiveQuickReply($data) {
		$fbId = $data->sender->id;

		CacheManager::storeMessages($fbId, $data->message->text);
		Log::info("Invoking command in quick_reply '" . $data->message->quick_reply->payload . "'");
		$this->getAction($data->message->quick_reply->payload)->invoke($fbId);
	}

	public function receivePostback($data) {
		$fbId = $data->sender->id;
		$command = $this->getAction($data->postback->payload);
		if ($command != null) {
			Log::info("Invoking command in postback '" . $data->postback->payload . "'");
			$command->invoke($fbId);
		} else {
			Log::error("BotManager::receivePostback", ["body" => json_encode($data)]);
		}
	}

	public function getAction($payload) {
		foreach($this->commands as $command) {
			if ($command->isCommand($payload)) {
				return $command;
			}
		}
		return null;
	}
}