<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cache extends Model
{
	/*
	 * User Profile json format
	 * {
	 *  'first_name' : 'Peter',
	 *  'last_name' : 'Chang',
	 *  'timezone' : -7,
	 *  'gender' : 'male'
	 * }
	 *
	 * Messages json format
	 * [
	 *  'first_value',
	 *  ...
	 * ]
	 */
	protected $table = 'caches';
	protected $fillable = ['fbId', 'userProfile', 'command', 'messages', 'value', 'currentEvent'];
	public $timestamps = true;

	public function getUserProfileAttribute($value) {
		return json_decode($value);
	}

	public function setUserProfileAttribute($value) {
		$this->attributes['userProfile'] = json_encode($value);
	}

	public function getMessagesAttribute($value) {
		return json_decode($value);
	}

	public function setMessagesAttribute($value) {
		$this->attributes['messages'] = json_encode($value);
	}

	public function getValueAttribute($value) {
		return json_decode($value);
	}

	public function setValueAttribute($value) {
		$this->attributes['value'] = json_encode($value);
	}
}