<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUrlsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UrlsController extends Controller
{
    public function index(): View
    {
        $perPage = 15;

        $urls = DB::table('urls')
            ->paginate($perPage);
        $urls->setPath('');

        return view('url.index', compact('urls'));
    }

    public function store(StoreUrlsRequest $request): RedirectResponse
    {
        $normalizedUrlName = $this->normalizeUrlName($request['url']['name']);

        $url = DB::table('urls')
            ->where('name', $normalizedUrlName)
            ->first();

        if ($url) {
            $id = $url->id;
            flash('Страница уже существует')->success();
            ;
        } else {
            $id = DB::table('urls')->insertGetId([
                'name' => $normalizedUrlName,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]);
            flash('Страница успешно добавлена')->success();
        }

        return redirect()->route('urls.show', $id);
    }

    public function show(int $id): View
    {
        $url = DB::table('urls')
            ->findOr($id, ['*'], function () {
                abort('404');
            });

        $urlChecks = DB::table('url_checks')
            ->where('url_id', $url->id)
            ->get();

        return view('url.show', compact('url', 'urlChecks'));
    }

    private function normalizeUrlName(string $url): string
    {
        ['scheme' => $scheme, 'host' => $host] = parse_url($url);

        return "{$scheme}://{$host}";
    }
}
