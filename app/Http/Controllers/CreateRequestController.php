<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;

class CreateRequestController extends Controller
{
	private $fbId = "1139072729524249";

	public function index()
	{
		return view("create_request.index");
	}

	public function message(Request $request)
	{
		return $this->sendRequest($this->makeMessageJSON($request->message));
	}

	public function postback($command)
	{
		return $this->sendRequest($this->makePostbackJSON($command));
	}

	public function quickreply($command, $text)
	{
		return $this->sendRequest($this->makeQuickReplyJSON(str_replace("_", " ", $text), $command));
	}

	public function sendRequest($data) {
		$webhook = new WebhookController();
		$webhook->receiveDebug($data);

		return redirect("chat");
	}

	public function makeMessageJSON($message)
	{
		return '{ "object": "page", "entry": [ { "id": "103987096773137", "time": 1484502780230, "messaging": [ { "sender": { "id": "' . $this->fbId . '" }, "recipient": { "id": "103987096773137" }, "timestamp": 1484502779985, "message": { "mid": "mid.1484502779985:f9e8599471", "seq": 351386, "text": "' . $message . '" } } ] } ] }';
	}

	public function makePostbackJSON($payload)
	{
		return '{ "object":"page", "entry":[ { "id":"103987096773137", "time":1484228000728, "messaging":[ { "recipient":{"id":"103987096773137"}, "timestamp":1484228000728, "sender":{"id":"1139072729524249"}, "postback":{"payload":"' . $payload . '"} } ] } ] }';
	}

	public function makeQuickReplyJSON($text, $payload)
	{
		return '{ "object": "page", "entry": [ { "id": "103987096773137", "time": 1484503433841, "messaging": [ { "sender": { "id": "' . $this->fbId . '" }, "recipient": { "id": "103987096773137" }, "timestamp": 1484503433595, "message": { "quick_reply": { "payload": "' . $payload . '" }, "mid": "mid.1484503433595:ca68478d74", "seq": 351390, "text": "' . $text . '" } } ] } ] }';
	}
}