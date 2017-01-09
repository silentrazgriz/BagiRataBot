<?php
namespace App\Core\BagiRata\Actions;

use App\Core\BagiRata\SendMessage;

class Members
{
	public function addMember($fbId, array $arguments)
	{
		$item = Events::getActiveEvent($fbId);
		if ($item) {
			$item->members = array_unique(array_merge($item->members, $arguments));
			$item->save();

			$this->sendMemberListMessage($fbId, $item);
		} else {
			SendMessage::callApi($fbId, "Anda belum memiliki event saat ini");
		}
	}

	public function removeMember($fbId, array $arguments)
	{
		$item = Events::getActiveEvent($fbId);
		if ($item) {
			$item->members = array_diff($item->members, $arguments);
			$item->save();

			$this->sendMemberListMessage($fbId, $item);
		} else {
			SendMessage::callApi($fbId, "Anda belum memiliki event saat ini");
		}
	}

	public function sendMemberListMessage($fbId, $item)
	{
		$message = "Daftar anggota pada event [$item->event]:\n";
		foreach ($item->members as $key => $member) {
			if ($key > 0) {
				$message .= "\n";
			}
			$message .= ($key + 1) . ". $member";
		}
		SendMessage::callApi($fbId, $message);
	}
}