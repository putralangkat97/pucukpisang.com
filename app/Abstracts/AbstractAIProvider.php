<?php

namespace App\Abstracts;

abstract class AbstractAIProvider
{
    abstract public function call(string $promp);
}
