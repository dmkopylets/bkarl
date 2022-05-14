<?php

namespace App\Application\Http\Auth;

class Password
{
    public function generateRandPassword(): string
    {
        return base_convert(uniqid('pass', true), 10, 36);
    }
}