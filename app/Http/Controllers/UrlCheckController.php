<?php

namespace App\Http\Controllers;

use DiDom\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UrlCheckController
{
    public function store(int $ulrId): RedirectResponse
    {
        $url = DB::table('urls')
            ->findOr($ulrId, ['*'], static function () {
                abort(404);
            });

        try {
            $response = Http::get($url->name);
        } catch (\Exception $exception) {
            $errorMessage = $exception->getMessage();
            flash($errorMessage)->error();

            return redirect()
                ->route('urls.show', $url->id)
                ->withErrors($errorMessage);
        }

        $document = new Document($response->body());
        DB::table('url_checks')
            ->insert([
                'url_id' => $url->id,
                'status_code' => $response->status(),
                'h1' => optional($document->first('h1'))->text(),
                'title' => optional($document->first('title'))->text(),
                'description' => optional($document->first('meta[name=description]'))
                    ->getAttribute('content'),
                'created_at' => now()->toDateTimeString(),
            ]);

        if ($response->status() !== 200) {
            flash('Проверка была выполнена успешно, но сервер ответил с ошибкой')->warning();
        } else {
            flash('Страница успешно проверена')->success();
        }

        return redirect()->route('urls.show', $url->id);
    }
}
