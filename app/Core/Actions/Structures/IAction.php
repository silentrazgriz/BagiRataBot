<?php
namespace App\Core\Actions\Structures;

interface IAction
{
	public function message($fbId);
	public function quickReply($fbId);
	public function postback($fbId);
}