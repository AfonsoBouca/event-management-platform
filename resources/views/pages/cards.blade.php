@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row g-4" id="cards">
        {{-- Render each card using the partial --}}
        @each('partials.card', $cards, 'card')

        {{-- Form for creating a new card --}}
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <form class="new_card">
                        <input type="text" name="name" class="form-control" placeholder="New card name...">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection