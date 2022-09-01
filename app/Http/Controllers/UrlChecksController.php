<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class UrlChecksController
{
    public function store(int $ulrId)
    {
        $url = DB::table('urls')->find($ulrId);
        if (!$url) {
            abort('404');
        }

        DB::table('url_checks')->insert([
            'url_id'     => $url->id,
            'status'     => 200,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        flash('Страница успешно проверена')->info();

        return redirect()
            ->route('urls.show', $url->id);
    }
}
