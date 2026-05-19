<?php

namespace App\Models;

use App\Services\PaymentService;
use Illuminate\Database\Eloquent\Model;

class Ezgo extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    public function resolveRouteBinding($value, $field = null): ?self
    {
        $items = app(PaymentService::class)->getEzgoList();

        $item = collect($items)->firstWhere('id', $value);

        if (! $item) {
            return null;
        }

        return static::fromApi($item);
    }

    public static function fromApi(array $data): self
    {
        $ezgo = new self;

        $ezgo->id = $data['id'] ?? null;
        $ezgo->service_name = $data['service_name'] ?? null;
        $ezgo->external_order_id = $data['external_order_id'] ?? null;
        $ezgo->transaction_id = $data['transaction_id'] ?? null;
        $ezgo->reference_number = $data['reference_number'] ?? null;
        $ezgo->amount = $data['amount'] ?? null;
        $ezgo->customer_phone = $data['customer_phone'] ?? null;
        $ezgo->type = $data['type'] ?? null;
        $ezgo->status = $data['status'] ?? null;
        $ezgo->request_payload = $data['request_payload'] ?? null;
        $ezgo->response_payload = $data['response_payload'] ?? null;
        $ezgo->created_at = $data['created_at'] ?? null;
        $ezgo->updated_at = $data['updated_at'] ?? null;
        $ezgo->paid_at = $data['paid_at'] ?? null;

        return $ezgo;
    }
}
