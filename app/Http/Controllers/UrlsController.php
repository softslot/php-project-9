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
        return view('url.index');
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
        $currentTime = now()->toDateTimeString();

        DB::table('urls')->insert([
            'name' => $normalizedName,
            'created_at' => $currentTime,
            'updated_at' => $currentTime,
        ]);

        $url = DB::table('urls')
            ->where('name', $normalizedName)
            ->first();

        if (!$url) {
            abort('404');
        }

        return redirect()
            ->route('urls.show', $url->id);
    }

    public function show(int $id): View
    {
        $url = DB::table('urls')->find($id);
        if (!$url) {
            abort('404');
        }

        return view('url.show', compact('url'));
    }
}
