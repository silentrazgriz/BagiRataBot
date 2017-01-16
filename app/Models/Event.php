<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
	/*
	 * Member json format
	 * ['member1', 'member2', ...]
	 *
	 * Transaction json format
	 * [
	 *  {
	 *    'info': 'lunch at yoshinoya',
	 *    'amount': 4000,
	 *    'payments': [
	 *      {
	 *        'member': 'member1',
	 *        'amount': 2000
	 *      },
	 *      ...
	 *    ]
	 *  },
	 *  ...
	 * ]
	 */
	protected $table = 'events';
	protected $fillable = ['fbId', 'name', 'members', 'transactions'];
	public $timestamps = true;

	public function getMembersAttribute($value) {
		return json_decode($value);
	}

	public function setMembersAttribute($value) {
		$this->attributes['members'] = json_encode($value);
	}

	public function getTransactionsAttribute($value) {
		return json_decode($value);
	}

	public function setTransactionsAttribute($value) {
		$this->attributes['transactions'] = json_encode($value);
	}

	public function totalPayment() {
		$total = 0;
		foreach ($this->transactions as $transaction) {
			foreach ($transaction->payments as $payment) {
				$total += $payment->amount;
			}
		}
		return $total;
	}

	public function totalPurchase() {
		$total = 0;
		foreach ($this->transactions as $transaction) {
			$total += $transaction->amount;
		}
		return $total;
	}

	public function purchasePerMember() {
		return $this->totalPurchase() / max(count($this->members), 1);
	}

	public function paymentData() {
		$paymentData = [];

		foreach ($this->members as $key => $member) {
			array_push($paymentData, ['member' => $member, 'payment' => 0, 'diff' => 0, 'transaction' => 0]);
		}

		foreach ($this->payments as $key => $payment) {
			foreach ($paymentData as &$data) {
				if ($data['member'] == $payment->member) {
					$data['payment'] += $payment->amount;
					$data['diff'] = $data['payment'] - $this->purchasePerMember();
					$data['transaction']++;
				}
			}
		}

		uasort($paymentData, array($this, 'comparePaymentData'));

		return $paymentData;
	}

	public function comparePaymentData($a, $b) {
		if ($a['diff'] == $b['diff']) {
			return 0;
		}
		return ($a['diff'] < $b['diff']) ? -1 : 1;
	}
}
