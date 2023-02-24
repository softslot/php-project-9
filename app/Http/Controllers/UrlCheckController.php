<?php

namespace App\Http\Controllers;

use DiDom\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class UrlCheckController
{
    public function store(int $ulrId): RedirectResponse
    {
        $url = DB::table('urls')->find($ulrId);

        abort_unless($url, 404);

        try {
            $response = Http::get($url->name);
        } catch (\GuzzleHttp\Exception\RequestException $exception) {
            $errorMessage = $exception->getMessage();
            flash($errorMessage)->error();

            return Response::redirectToRoute('urls.show', $url->id)
                ->withErrors($errorMessage);
        }

        $document = new Document($response->body());

        $h1 = optional($document->first('h1'))->text();
        $title = optional($document->first('title'))->text();
        $description = optional($document->first('meta[name=description]'))->getAttribute('content');
        $data = [
            'url_id' => $url->id,
            'status_code' => $response->status(),
            'h1' => $h1,
            'title' => $title,
            'description' => $description,
            'created_at' => now()->toDateTimeString(),
        ];

        DB::table('url_checks')->insert([$data]);

        if ($response->serverError()) {
            flash('Проверка была выполнена успешно, но сервер ответил с ошибкой')->warning();
        } else {
            flash('Страница успешно проверена')->success();
        }

        return Response::redirectToRoute('urls.show', $url->id);
    }
}
