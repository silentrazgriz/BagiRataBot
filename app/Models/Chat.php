<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
	protected $table = 'chats';
	protected $fillable = ['quickReplies', 'message'];
	public $timestamps = true;

	public function getQuickRepliesAttribute($value) {
		return json_decode($value);
	}

	public function setQuickRepliesAttribute($value) {
		$this->attributes['quickReplies'] = json_encode($value);
	}
}