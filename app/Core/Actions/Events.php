<?php
namespace App\Core\BagiRata\Actions;

use App\Core\BagiRata\SendMessage;
use App\Models\BagiRata\Event;
use Illuminate\Support\Facades\DB;

class Events
{
	public function getEventList($fbId)
	{
		$events = Event::where('fbId', $fbId)->get();
		$message = "Anda belum memiliki event";
		if (count($events) > 0) {
			$message = "Daftar event anda:\n";
			foreach ($events as $key => $item) {
				$message .= ($key + 1) . ". $item->event\n";
			}
		}

		SendMessage::callApi($fbId, $message);
	}

	public function setEventSession($fbId, array $arguments)
	{
		$event = $arguments[0];

		DB::transaction(function () use ($fbId, $event) {
			Event::where('fbId', $fbId)->update(['isActive' => false]);
			Event::where('fbId', $fbId)->where('event', $event)->update(['isActive' => true]);
			SendMessage::callApi($fbId, "[$event] terpilih sebagai event berjalan");
		});
	}

	public function getEventDetail($fbId, array $arguments)
	{
		$event = $arguments[0];

		Transactions::sendTransactionDetailMessage($fbId, $event, true);
	}

	public function getEventSummary($fbId, array $arguments)
	{
		$event = $arguments[0];

		Transactions::sendTransactionDetailMessage($fbId, $event, false);
	}

	public function addEvent($fbId, array $arguments)
	{
		$event = $arguments[0];

		if (Event::where('fbId', $fbId)->where('event', $event)->count() == 0) {
			DB::transaction(function () use ($fbId, $event, $arguments) {
				Event::create([
					'fbId' => $fbId,
					'event' => $event,
					'members' => [],
					'purchases' => [],
					'payments' => [],
					'isActive' => false
				]);
				SendMessage::callApi($fbId, "[$event] berhasil dibuat");
				$this->setEventSession($fbId, $arguments);
			});
		} else {
			SendMessage::callApi($fbId, "[$event] sudah ada, silahkan tambahkan event baru");
		}
	}

	public function removeEvent($fbId, array $arguments)
	{
		$event = $arguments[0];

		DB::transaction(function () use ($fbId, $event) {
			Event::where('fbId', $fbId)->where('event', $event)->delete();
			SendMessage::callApi($fbId, "[$event] berhasil dihapus");

			if (!self::getActiveEvent($fbId) && Event::where('fbId', $fbId)->count() > 0) {
				$this->setEventSession($fbId, array(Event::where('fbId', $event)->orderBy('id', 'desc')->first()->event));
			}
		});
	}

	public function removeAllEvent($fbId)
	{
		DB::transaction(function () use ($fbId) {
			Event::where('fbId', $fbId)->delete();
			SendMessage::callApi($fbId, "Seluruh event berhasil dihapus");
		});
	}

	public static function getActiveEvent($fbId)
	{
		return Event::where('fbId', $fbId)->where('isActive', true)->first();
	}
}