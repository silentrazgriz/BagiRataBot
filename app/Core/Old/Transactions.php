<?php
namespace App\Core\BagiRata\Actions;

use App\Core\BagiRata\SendMessage;
use App\Models\BagiRata\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Transactions
{
	public function addPurchase($fbId, array $arguments)
	{
		$amount = $arguments[0];
		$info = isset($arguments[1]) ? $arguments[1] : "";

		$item = Events::getActiveEvent($fbId);
		DB::transaction(function () use ($fbId, $item, $amount, $info) {
			$item->purchases = array_merge($item->purchases, [['amount' => $amount, 'info' => $info, 'created' => Carbon::now()->toDateTimeString()]]);
			$item->save();
		});

		SendMessage::callApi($fbId, "Transaksi sebesar [" . number_format($amount) . "] untuk [$info] di [$item->event]");
		self::sendTransactionListMessage($fbId, $item->event);
	}

	public function addPayment($fbId, array $arguments)
	{
		$amount = $arguments[0];
		$member = $arguments[1];
		$info = isset($arguments[2]) ? $arguments[2] : "";

		$item = Events::getActiveEvent($fbId);
		DB::transaction(function () use ($fbId, $item, $amount, $member, $info) {
			if (in_array($member, $item->members)) {
				$item->payments = array_merge($item->payments, [['amount' => $amount, 'member' => $member, 'info' => $info, 'created' => Carbon::now()->toDateTimeString()]]);
				$item->save();
			} else {
				SendMessage::callApi($fbId, "Nama anggota yang anda masukkan tidak terdaftar");
			}
		});

		SendMessage::callApi($fbId, "Pembayaran sebesar [" . number_format($amount) . "] oleh [$member] untuk [$info] di [$item->event]");
		self::sendPaymentListMessage($fbId, $item->event);
	}

	public function removePurchase($fbId, array $arguments)
	{
		$id = $arguments[0] - 1;

		$item = Events::getActiveEvent($fbId);
		if ($id < count($item->purchases)) {
			array_splice($item->purchases, $id, 1);
			$item->save();
		} else {
			SendMessage::callApi($fbId, "Nomor transaksi yang anda masukkan tidak ditemukan");
		}

		SendMessage::callApi($fbId, "Transaksi nomor [$id] dihapus");
		self::sendTransactionListMessage($fbId, $item->event);
	}

	public function removePayment($fbId, array $arguments)
	{
		$id = $arguments[0] - 1;

		$item = Events::getActiveEvent($fbId);
		if ($id < count($item->payments)) {
			array_splice($item->payments, $id, 1);
			$item->save();
		} else {
			SendMessage::callApi($fbId, "Nomor pembayaran yang anda masukkan tidak ditemukan");
		}

		SendMessage::callApi($fbId, "Pembayaran nomor [$id] dihapus");
		self::sendPaymentListMessage($fbId, $item->event);
	}

	public static function sendTransactionListMessage($fbId, $event)
	{
		$item = Event::where('fbId', $fbId)->where('event', $event)->first();

		$message = "Daftar transaksi [$item->event]:\n\n";
		foreach ($item->purchases as $key => $purchase) {
			$message .= ($key + 1) . ". [" . number_format($purchase->amount) . "] untuk [$purchase->info] pada [$purchase->created]\n";
		}
		$message .= "\n";

		SendMessage::callApi($fbId, $message);
	}

	public static function sendPaymentListMessage($fbId, $event)
	{
		$item = Event::where('fbId', $fbId)->where('event', $event)->first();

		$message = "Daftar pembayaran [$item->event]:\n\n";
		foreach ($item->payments as $key => $payment) {
			$message .= ($key+1) . ". [$payment->member] membayar [" . number_format($payment->amount) . "] untuk [$payment->info] pada [$payment->created]\n";
		}
		$message .= "\n";

		SendMessage::callApi($fbId, $message);
	}

	public static function sendPaymentDetailMessage($fbId, $event)
	{
		$item = Event::where('fbId', $fbId)->where('event', $event)->first();

		$message = "Pembagian biaya [$item->event]:\n\n";
		$paymentData = $item->paymentData();
		foreach ($paymentData as &$first) {
			if ($first['diff'] < 0) {
				foreach ($paymentData as &$second) {
					if ($first['member'] != $second['member'] && $first['diff'] < 0 && $second['diff'] > 0) {
						$owe = min(abs($first['diff']), $second['diff']);
						$first['diff'] += $owe;
						$second['diff'] -= $owe;
						$message .= "[" . $first['member'] . "] membayar ke [" . $second['member'] . "] sejumlah [" . number_format($owe) . "]\n";
					}
				}
			}
		}

		SendMessage::callApi($fbId, $message);
	}

	public static function sendTransactionDetailMessage($fbId, $event, $detail)
	{
		$item = Event::where('fbId', $fbId)->where('event', $event)->first();

		if ($detail) {
			self::sendTransactionListMessage($fbId, $event);
			self::sendPaymentListMessage($fbId, $event);
		}

		$message = "Detail [$event] pada [$item->created_at]:\n\n" .
			"Total transaksi [" . number_format($item->totalPurchase()) . "]\n" .
			"Total bayar [" . number_format($item->totalPayment()) . "]\n" .
			"Jumlah anggota [" . count($item->members) . "] orang\n" .
			"Biaya per orang [" . number_format($item->averagePurchase()) . "]\n\n";

		SendMessage::callApi($fbId, $message);
		self::sendPaymentDetailMessage($fbId, $event);
	}
}