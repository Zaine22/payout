<?php

namespace App\Livewire\Synthesizers;

use App\Models\Payment;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class PaymentSynth extends Synth
{
    public static $key = 'payment_synth';

    public static function match($target)
    {
        return $target instanceof Payment;
    }

    public function dehydrate($target)
    {
        // Convert the model to an array of its attributes so it can be sent to the frontend
        return [$target->toArray(), []];
    }

    public function hydrate($data, $meta)
    {
        $payment = new Payment;
        $payment->forceFill($data);
        $payment->exists = true;

        return $payment;
    }

    public function get(&$target, $key)
    {
        return $target->{$key};
    }

    public function set(&$target, $key, $value)
    {
        $target->{$key} = $value;
    }
}
