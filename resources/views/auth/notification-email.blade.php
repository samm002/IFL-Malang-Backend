@extends('layouts.template')

@section('content')
    <div class="notification-email">
        <img class="icon" src={{ $isErrorImg ? asset('/assets/image/error.webp') : asset('/assets/image/check.webp') }} alt="icon">
        <h1>{{ $title }}</h1>
        <p>{{ $message }}</p>
    </div>
@endsection