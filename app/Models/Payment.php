<?php

namespace App\Models;

use App\Services\PaymentService;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    public function resolveRouteBinding($value, $field = null): ?self
    {
        $data = app(PaymentService::class)->find((int) $value);

        if (! $data) {
            return null;
        }

        return static::fromApi($data);
    }

    public static function fromApi(array $data): self
    {
        $payment = new self;

        $userInfo = $data['user_info'] ?? [];

        $payment->id = $data['id'] ?? null;
        $payment->money = $data['money'] ?? null;
        $payment->orderno = $data['orderno'] ?? null;
        $payment->account_bank = $data['account_bank'] ?? null;
        $payment->account = $data['account'] ?? null;
        $payment->name = $data['name'] ?? null;
        $payment->status = $data['status'] ?? null;
        $payment->addtime = $data['addtime'] ?? null;

        $payment->user_id = $userInfo['id'] ?? null;
        $payment->user_nickname = $userInfo['user_nickname'] ?? null;
        $payment->avatar = $userInfo['avatar'] ?? null;
        $payment->mobile = $userInfo['mobile'] ?? null;

        return $payment;
    }
}
