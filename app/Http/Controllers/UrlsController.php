<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class UrlsController extends Controller
{
    public function index(): View
    {
        $perPage = 15;

        $urls = DB::table('urls')
            ->select(['id', 'name'])
            ->paginate($perPage);

        $urlIds = collect($urls->items())
            ->pluck('id');

        $urlChecks = DB::table('url_checks')
            ->whereIn('url_id', $urlIds)
            ->orderBy('created_at')
            ->get()
            ->keyBy('url_id');

        return view('url.index', compact('urls', 'urlChecks'));
    }

    public function store(Request $request): \Illuminate\Http\Response | \Illuminate\Http\RedirectResponse
    {
        $validator = Validator::make(
            $request->all(),
            ['url.name' => 'required|max:255|url'],
            ['*' => 'Некорректный URL'],
        );

        if ($validator->fails()) {
            $errors = $validator->errors();

            return Response::view('index', compact('errors'))
                ->setStatusCode(422);
        }

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

        return Response::redirectToRoute('urls.show', $id);
    }

    public function show(int $id): View
    {
        $url = DB::table('urls')
            ->findOr($id, ['*'], function () {
                abort(404);
            });

        $urlChecks = DB::table('url_checks')
            ->where('url_id', $url->id)
            ->get();

        return view('url.show', compact('url', 'urlChecks'));
    }

    /**
     * @throws \Exception
     */
    private function normalizeUrlName(string $url): string
    {
        $data = parse_url($url);
        if (!is_array(parse_url($url))) {
            throw new \Exception('URL parsed error!');
        }

        return "{$data['scheme']}://{$data['host']}";
    }
}
