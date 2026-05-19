<?php

namespace App\Livewire\Synthesizers;

use App\Models\Ezgo;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class EzgoSynth extends Synth
{
    public static $key = 'ezgo_synth';

    public static function match($target)
    {
        return $target instanceof Ezgo;
    }

    public function dehydrate($target)
    {
        return [$target->toArray(), []];
    }

    public function hydrate($data, $meta)
    {
        $ezgo = new Ezgo;
        $ezgo->forceFill($data);
        $ezgo->exists = true;

        return $ezgo;
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
