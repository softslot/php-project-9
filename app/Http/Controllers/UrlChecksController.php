<?php

namespace App\Http\Controllers;

use DiDom\Document;
use DiDom\Query;
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

        try {
            $response = Http::get($url->name);
        } catch (\Exception) {
            flash('Произошла ошибка при проверке, не удалось подключиться')->error();

            return redirect()->route('urls.show', $url->id);
        }

        $document = new Document($response->body());
        $h1 = optional($document->first('h1'))->text();
        $title = optional($document->first('title'))->text();
        $description = optional(
            $document->first("//meta[contains(@name, 'description')]",
                Query::TYPE_XPATH)
        )->getAttribute('content');

        DB::table('url_checks')
            ->insert([
                'url_id' => $url->id,
                'status_code' => $response->status(),
                'h1' => $h1,
                'title' => $title,
                'description' => $description,
                'created_at' => now()->toDateTimeString(),
            ]);

        if ($response->status() === 200) {
            flash('Страница успешно проверена')->success();
        } else {
            flash('Проверка была выполнена успешно, но сервер ответил с ошибкой')->warning();
        }

        return redirect()->route('urls.show', $url->id);
    }
}
