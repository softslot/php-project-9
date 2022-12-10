@extends('layouts.app')

@section('content')
    <h1 class="mb-3">Сайты</h1>
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-nowrap" data-test="urls">
            <tr>
                <th>ID</th>
                <th>Имя</th>
                <th>Последняя проверка</th>
                <th>Код ответа</th>
            </tr>
            @foreach($urls as $url)
                <tr>
                    <td>{{ $url->id }}</td>
                    <td><a href="{{ route('urls.show', $url->id) }}">{{ $url->name }}</a></td>
                    <td>{{ $urlChecks->get($url->id)->created_at ?? '' }}</td>
                    <td>{{ $urlChecks->get($url->id)->status_code ?? '' }}</td>
                </tr>
            @endforeach
        </table>

        {{ $urls->links('vendor.pagination.bootstrap-5') }}
    </div>
@endsection
