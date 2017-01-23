<?php
/**
 * Created by PhpStorm.
 * User: LS
 * Date: 1/9/2017
 * Time: 1:34 PM
 */

namespace App\Http\Controllers;

use App\Core\BotManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
	public function subscribe(Request $request) {
		$data = $request->all();
		if ($data["hub_mode"] === "subscribe" &&	$data["hub_verify_token"] === "absoluteforces") {
			return response($data["hub_challenge"], 200);
		}

		return abort(403);
	}

	public function receiveDebug($data) {
		Log::info("WebhookController::receiveDebug", ["content" => $data]);

		$data = json_decode($data);
		$manager = new BotManager();
		if ($data->object == "page") {
			foreach ($data->entry as $entry) {
				foreach ($entry->messaging as $message) {
					if (isset($message->message)) {
						if (isset($message->message->quick_reply)) {
							Log::info("Processing quick reply");
							$manager->receiveQuickReply($message);
						} else {
							Log::info("Processing message");
							$manager->receiveMessage($message);
						}
					} else if (isset($message->postback)) {
						Log::info("Processing postback");
						$manager->receivePostback($message);
					}
				}
			}
		}
	}
	
	public function receiveMessage(Request $request) {
		Log::info("WebhookController::receiveMessage", ["content" => $request->getContent()]);

		try {
			$data = json_decode($request->getContent());
			$manager = new BotManager();
			if ($data->object == "page") {
				foreach ($data->entry as $entry) {
					foreach ($entry->messaging as $message) {
						if (isset($message->message)) {
							if (isset($message->message->quick_reply)) {
								Log::info("Processing quick reply");
								$manager->receiveQuickReply($message);
							} else {
								Log::info("Processing message");
								$manager->receiveMessage($message);
							}
						} else if (isset($message->postback)) {
							Log::info("Processing postback");
							$manager->receivePostback($message);
						}
					}
				}
			}
		} catch (\Exception $e) {
			Log::error("WebhookController::receiveMessage", ["code" => $e->getCode(), "message" => $e->getMessage()]);
		}
		
		return response("Message received", 200);
	}
}