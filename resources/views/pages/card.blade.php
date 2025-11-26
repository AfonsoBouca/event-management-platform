@extends('layouts.app')

@section('title', $card->name . ' | ' . config('app.name'))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center" id="cards">
        {{-- Render a single card using the partial --}}
        @include('partials.card', ['card' => $card])
    </div>
</div>
@endsection