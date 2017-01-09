<?php

namespace App\Models\BagiRata;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
	protected $table = 'br_events';
	protected $fillable = ['fbId', 'event', 'members', 'purchases', 'payments', 'isActive'];
	public $timestamps = true;

	public function totalPurchase() {
		$totalPurchase = 0;
		foreach ($this->purchases as $purchase) {
			$totalPurchase += $purchase->amount;
		}
		return $totalPurchase;
	}

	public function totalPayment() {
		$totalPayment = 0;
		foreach ($this->payments as $payment) {
			$totalPayment += $payment->amount;
		}
		return $totalPayment;
	}

	public function averagePurchase() {
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
					$data['diff'] = $data['payment'] - $this->averagePurchase();
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

	public function getMembersAttribute($value) {
		return json_decode($value);
	}

	public function setMembersAttribute($value) {
		$this->attributes['members'] = json_encode($value);
	}

	public function getPurchasesAttribute($value) {
		return json_decode($value);
	}

	public function setPurchasesAttribute($value) {
		$this->attributes['purchases'] = json_encode($value);
	}

	public function getPaymentsAttribute($value) {
		return json_decode($value);
	}

	public function setPaymentsAttribute($value) {
		$this->attributes['payments'] = json_encode($value);
	}
}
