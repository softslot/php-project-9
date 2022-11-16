<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UrlChecksController
{
    public function store(int $ulrId): RedirectResponse
    {
        $url = DB::table('urls')
            ->findOr($ulrId, ['*'], static function () {
                abort('404');
            });

        $response = Http::get($url->name);

        DB::table('url_checks')
            ->insert([
                'url_id' => $url->id,
                'status' => $response->status(),
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]);

        flash('Страница успешно проверена')->info();

        return redirect()->route('urls.show', $url->id);
    }
}
