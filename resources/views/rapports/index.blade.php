@extends('layouts.app')

@section('titre', __('app.rapports.titre'))

@section('content')
    <div class="page-head">
        <h1>{{ __('app.rapports.titre') }}</h1>
    </div>

    <div class="grid-kpi">
        @foreach (\App\Services\RapportService::TYPES as $type)
            <div class="kpi" style="justify-content:center">
                <a class="btn secondary" href="{{ route('rapports.show', $type) }}" style="justify-content:center">{{ __('app.rapports.types.'.$type) }}</a>
            </div>
        @endforeach
    </div>
@endsection
