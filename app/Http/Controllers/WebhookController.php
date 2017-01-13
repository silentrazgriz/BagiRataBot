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
		if ($data['hub_mode'] === 'subscribe' &&	$data['hub_verify_token'] === 'absoluteforces') {
			return response($data['hub_challenge'], 200);
		}

		return abort(403);
	}
	
	public function receiveMessage(Request $request) {
		Log::info('Message received', ['content' => $request->getContent()]);
		
		try {
			$data = json_decode($request->getContent());
			$manager = new BotManager();
			if ($data->object == 'page') {
				foreach ($data->entry as $entry) {
					foreach ($entry->messaging as $message) {
						if (isset($message->message)) {
							$manager->receivedMessage($message);
						}
					}
				}
			}
		} catch (\Exception $e) {
			Log::error('Error while receiving message', ['code' => $e->getCode(), 'message' => $e->getMessage()]);
		}
		
		return response('Message received', 200);
	}
}