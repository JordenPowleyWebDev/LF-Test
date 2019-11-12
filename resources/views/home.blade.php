@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Hello {{ $user->name }}</div>

                <div class="card-body">
                    <div id="user-forecast" user="{{ $user->id }}"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
