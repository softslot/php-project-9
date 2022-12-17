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
        $url = DB::table('urls')->find($ulrId);

        abort_unless($url, 404);

        try {
            $response = Http::get($url->name);
            $document = new Document($response->getBody()->getContents());
        } catch (\Exception $exception) {
            $errorMessage = $exception->getMessage();
            flash($errorMessage)->error();

            return redirect()
                ->route('urls.show', $url->id)
                ->withErrors($errorMessage);
        }

        DB::table('url_checks')
            ->insert([
                'url_id' => $url->id,
                'status_code' => $response->getStatusCode(),
                'h1' => optional($document->first('h1'))->text(),
                'title' => optional($document->first('title'))->text(),
                'description' => optional($document->first('meta[name=description]'))
                    ->getAttribute('content'),
                'created_at' => now()->toDateTimeString(),
            ]);

        if (false) {
            flash('Проверка была выполнена успешно, но сервер ответил с ошибкой')->warning();
        } else {
            flash('Страница успешно проверена')->success();
        }

        return redirect()->route('urls.show', $url->id);
    }
}
