<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ViewErrorBag;

class UrlController extends Controller
{
    public function index(): \Illuminate\Http\Response
    {
        $urls = DB::table('urls')
            ->select(['id', 'name'])
            ->orderByDesc('created_at')
            ->paginate();

        $urlIds = collect($urls->items())
            ->pluck('id');

        $urlChecks = DB::table('url_checks')
            ->whereIn('url_id', $urlIds)
            ->orderBy('created_at')
            ->get()
            ->keyBy('url_id');

        return Response::view('url.index', compact('urls', 'urlChecks'));
    }

    public function store(Request $request): \Illuminate\Http\Response | \Illuminate\Http\RedirectResponse
    {
        $validator = Validator::make(
            $request->all(),
            ['url.name' => 'required|max:255|url'],
        );

        if ($validator->fails()) {
            flash('Некорректный URL')->error();

            session()->flash('errors', (new ViewErrorBag())->put(
                'default',
                $validator->messages()
            ));

            return Response::view('index')
                ->setStatusCode(422);
        }

        $normalizedUrlName = $this->normalizeUrlName($request['url']['name']);

        $url = DB::table('urls')
            ->where('name', $normalizedUrlName)
            ->first();

        if ($url) {
            $id = $url->id;
            flash('Страница уже существует')->success();
        } else {
            $id = DB::table('urls')->insertGetId([
                'name' => $normalizedUrlName,
                'created_at' => now()->toDateTimeString(),
            ]);
            flash('Страница успешно добавлена')->success();
        }

        return Response::redirectToRoute('urls.show', $id);
    }

    public function show(int $id): \Illuminate\Http\Response
    {
        $url = DB::table('urls')->find($id);

        abort_unless($url, 404);

        $urlChecks = DB::table('url_checks')
            ->where('url_id', $url->id)
            ->orderByDesc('created_at')
            ->get();

        return Response::view('url.show', compact('url', 'urlChecks'));
    }

    private function normalizeUrlName(string $url): string
    {
        $data = parse_url($url);

        return "{$data['scheme']}://{$data['host']}";
    }
}
