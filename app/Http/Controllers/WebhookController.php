<?php
/**
 * Created by PhpStorm.
 * User: LS
 * Date: 1/9/2017
 * Time: 1:34 PM
 */

namespace App\Http\Controllers;

use App\Core\BagiRata\BotManager;
use App\Models\ResponseList;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
	public function subscribe(Request $request) {
		$data = $request->all();
		if ($data['hub_mode'] === 'subscribe' &&	$data['hub_verify_token'] === 'absoluteforces') {
			return response($data['hub_challenge'], 200);
		} else {
			abort(403, json_encode($data));
		}
	}
	
	public function receiveMessage(Request $request) {
		ResponseList::create(['response' => $request->getContent()]);
		
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
			ResponseList::create(['response' => $e->getMessage()]);
		}
		
		return response('Message received', 200);
	}
	
	public function processMessage(Request $request) {
		
	}
}