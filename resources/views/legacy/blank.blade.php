@extends('layout.default')

@foreach ($body as $content)
    @include($content['template'], $content)
@endforeach

@section('head')
    @parent

    @stack('page_scripts')
@endsection

@section('body')
    @stack('page_content')
@endsection
