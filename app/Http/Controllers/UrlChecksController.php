<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UrlChecksController
{
    public function store(int $ulrId)
    {
        dd($ulrId);
        return __METHOD__;
    }
}
