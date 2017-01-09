<?php
namespace App\Core\BagiRata;


use App\Core\BagiRata\Actions\Events;
use App\Core\BagiRata\Actions\Members;
use App\Core\BagiRata\Actions\Transactions;

class BotManager
{
	public $commands = [];
	public $helpMessage = "", $commandListMessage = [];

	public function __construct() {
		$this->commands = [
				new Command('+event', Events::class, 'addEvent', false, 1, 'Event'),
				new Command('-event', Events::class, 'removeEvent', false, 1, 'Event'),
				new Command('--event', Events::class, 'removeAllEvent', false, 0),
				new Command('list', Events::class, 'getEventList', false, 0),
				new Command('sum', Events::class, 'getEventSummary', false, 1, 'Event'),
				new Command('get', Events::class, 'getEventDetail', false, 1, 'Event'),
				new Command('set', Events::class, 'setEventSession', false, 1, 'Event'),
				new Command('+member', Members::class, 'addMember', true, 1, 'Anggota', true),
				new Command('-member', Members::class, 'removeMember', true, 1, 'Anggota', true),
				new Command('+purchase', Transactions::class, 'addPurchase', true, 1, 'Nominal transaksi', true),
				new Command('-purchase', Transactions::class, 'removePurchase', true, 1, 'Nomor transaksi', false),
				new Command('+pay', Transactions::class, 'addPayment', true, 2, 'Nominal pembayaran atau nama pembayar', true),
				new Command('-pay', Transactions::class, 'removePayment', true, 1, 'Nomor pembayaran', false)
		];
		$this->helpMessage = "Selamat datang di BagiRata App\n" .
			"Bot ini bisa digunakan untuk membagi biaya patungan dari beberapa orang secara otomatis\n\n" .
			"Panduan pengguna:\n" .
			"1. Bot ini digunakan dengan cara menggunakan perintah-perintah yang telah ditentukan\n" .
			"2. Perintah yang ditulis harus sesuai dengan format yang diminta\n" .
			"3. Untuk saat ini bot belum bisa memproses kata-kata yang berisi spasi";
		$this->commandListMessage[0] = "Daftar perintah yang tersedia (1/2):\n" .
			"[+event <nama_event>] untuk menambahkan event baru, contoh: '+event jakarta2017'\n" .
			"[+member <nama_anggota> <nama_anggota> ...] untuk menambahkan anggota ke dalam event, contoh: '+member daniel' atau '+member budi andi ben'\n" .
			"[+purchase <nominal_transaksi> <info>] untuk menambahkan transaksi, contoh: '+purchase 50000 yoshinoya'\n" .
			"[+pay <nominal_transaksi> <nama_pembayar> <info>] untuk menambahkan pembayaran, contoh: '+pay 30000 daniel'";
		$this->commandListMessage[1] = "Daftar perintah yang tersedia (2/2):\n" .
			"[list] untuk melihat daftar event\n" .
			"[set <nama_event>] untuk memilih event aktif, contoh: 'set jakarta2017'\n" .
			"[get <nama_event>] untuk melihat detail transaksi, pembayaran serta pembagian biaya pada event, contoh: 'get jakarta2017'\n" .
			"[sum <nama_event>] untuk melihat rangkuman transaksi dan pembagian biaya pada event, contoh: 'sum jakarta2017'";
	}

	public function receivedMessage($message) {
		$fbId = $message->sender->id;
		if (isset($message->attachments)) {
			SendMessage::callApi($fbId, "Attachment belum bisa digunakan");
		} else {
			$command = $this->parseMessages($message->message->text);
			if ($command == null) {
				SendMessage::callApi($fbId, $this->helpMessage);
				SendMessage::callApiArray($fbId, $this->commandListMessage);
			} else {
				if ($command['action']->checkActiveEvent($fbId)) {
					$command['action']->run($fbId, $command['values']);
				} else {
					SendMessage::callApi($fbId, "Anda belum memiliki event saat ini");
				}
			}
		}
	}

	public function parseMessages($message) {
		$message = strtolower($message);
		$tokens = explode(' ', $message);
		$action = $this->getAction($tokens[0]);
		if ($action == null) {
			return null;
		}

		$result['action'] = $action;
		$result['values'] = array_slice($tokens, 1);
		return $result;
	}

	public function getAction($keyword) {
		foreach($this->commands as $command) {
			if ($command->isCommand($keyword)) {
				return $command;
			}
		}
		return null;
	}
}