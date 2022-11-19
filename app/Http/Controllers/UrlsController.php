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
            ->select(['id', 'name'])
            ->paginate($perPage);

        $urlIds = $urls->pluck('id');

        $urlChecks = DB::table('url_checks')
            ->whereIn('url_id', $urlIds)
            ->orderBy('created_at')
            ->get()
            ->keyBy('url_id');

        return view('url.index', compact('urls', 'urlChecks'));
    }

    public function store(StoreUrlsRequest $request): RedirectResponse
    {
        $normalizedUrlName = $this->normalizeUrlName($request['url']['name']);

        $url = DB::table('urls')
            ->where('name', $normalizedUrlName)
            ->first();

        if ($url) {
            $id = $url->id;
            $flashMessage = 'Страница уже существует';
        } else {
            $id = DB::table('urls')->insertGetId([
                'name' => $normalizedUrlName,
                'created_at' => now()->toDateTimeString(),
            ]);
            $flashMessage = 'Страница успешно добавлена';
        }

        flash($flashMessage)->success();

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
