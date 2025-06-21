<?php

namespace App\Abstracts\AI;

abstract class AbstractAIProvider
{
    abstract public function call(string $promp);
}
