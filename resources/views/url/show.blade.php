@extends('layouts.app')

@section('content')
    <div class="container-lg mt-3">
        <h1>Сайт: {{ $url->name }}</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-nowrap" data-test="url">
                <tr>
                    <td>ID</td>
                    <td>{{ $url->id }}</td>
                </tr>
                <tr>
                    <td>Имя</td>
                    <td>{{ $url->name }}</td>
                </tr>
                <tr>
                    <td>Дата создания</td>
                    <td>{{ $url->created_at }}</td>
                </tr>
            </table>
        </div>
        <h2 class="mt-5 mb-3">Проверки</h2>
        <form method="post" action="{{ route('url_checks.store', $url->id) }}">
            @csrf
            <input type="submit" class="btn btn-primary mb-3" value="Запустить проверку">
        </form>
        <table class="table table-bordered table-hover" data-test="checks">
            <tr>
                <th>ID</th>
                <th>Код ответа</th>
                <th>h1</th>
                <th>title</th>
                <th>description</th>
                <th>Дата создания</th>
            </tr>
            @foreach($urlChecks as $urlCheck)
            <tr>
                <td>{{ $urlCheck->id }}</td>
                <td>{{ $urlCheck->status_code }}</td>
                <td>{{ $urlCheck->h1 }}</td>
                <td>{{ $urlCheck->title }}</td>
                <td>{{ $urlCheck->description }}</td>
                <td>{{ $urlCheck->created_at }}</td>
            </tr>
            @endforeach
        </table>
    </div>
@endsection
