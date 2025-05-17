@extends('layout')

@section('content')
    <div class="d-flex justify-content-center align-items-center" style="height: 80vh;">
        <div class="text-center">
            <h2>Selamat Datang {{ Auth::user()->name }} 👋</h2>
            <p class="text-muted">di Dashboard</p>
        </div>
    </div>
@endsection
