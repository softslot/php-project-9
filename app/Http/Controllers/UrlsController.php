<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UrlsController extends Controller
{
    public function index(): View
    {
        $perPage = 15;

        $urls = DB::table('urls')
            ->paginate($perPage);

        $urlsChecks = DB::table('url_checks')
            ->whereIn('url_id', $urls->pluck('id'))
            ->orderByDesc('created_at')
            ->groupBy('url_id')
            ->get();

        $urls->each(function ($url) use ($urlsChecks) {
            $check = $urlsChecks->firstWhere('url_id', $url->id);
            if ($check === null) {
                return $url;
            }

            $url->last_check = $check->created_at;
            $url->status = $check->status;

            return $url;
        });

        return view('url.index', compact('urls'));
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validate(
            $request,
            ['url.name' => 'required|max:255|url'],
            ['*' => 'Некорректный URL']
        );

        ['scheme' => $scheme, 'host' => $host] = parse_url($validated['url']['name']);
        $normalizedName = "{$scheme}://{$host}";

        $url = DB::table('urls')
            ->where('name', $normalizedName)
            ->first();

        if ($url) {
            flash('Страница уже существует');

            return redirect()
                ->route('urls.show', $url->id);
        }

        DB::table('urls')->insert([
            'name' => $normalizedName,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        $newUrl = DB::table('urls')
            ->where('name', $normalizedName)
            ->first();

        if (!$newUrl) {
            abort('404');
        }

        flash('Страница успешно добавлена')->success();

        return redirect()
            ->route('urls.show', $newUrl->id);
    }

    public function show(int $id): View
    {
        $url = DB::table('urls')->find($id);
        if (!$url) {
            abort('404');
        }

        $urlChecks = DB::table('url_checks')
            ->where('url_id', $url->id)
            ->get();

        return view('url.show', compact('url', 'urlChecks'));
    }
}
