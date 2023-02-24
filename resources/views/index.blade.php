@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="row">
            <div class="col-12 col-md-10 col-lg-8 mx-auto border rounded-3 bg-light p-5">
                <h1 class="display-3">Анализатор страниц</h1>
                <p class="lead">Бесплатно проверяйте сайты на SEO пригодность</p>
                <form action="{{ route('urls.index') }}" method="post" class="row" required>
                    @csrf
                    <div class="col-8">
                        <input
                        type="text"
                        name="url[name]"
                        value="{{ old('url') }}"
                        class="form-control form-control-lg @if(isset($validationErrors)) is-invalid @endif"
                        placeholder="https://www.example.com" />

                        @if(isset($validationErrors))
                            <div class="invalid-feedback">{{ $validationErrors->first() }}</div>
                        @endif
                    </div>

                    <div class="col-2">
                        <input type="submit" class="btn btn-primary btn-lg ms-3 px-5 text-uppercase mx-3" value="Проверить">
                    </div>
                </form>
        
            </div>
        </div>
    </div>
@endsection
