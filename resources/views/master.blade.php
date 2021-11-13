@extends('adminlte::page')

@section("title-suffix"){{config('adminlte.title')}}@endsection

@push("css")
    <link rel="stylesheet" href="{{asset('/vendor/flag-icon-css/css/flag-icon.min.css')}}">
    @livewireStyles
@endpush

@push("js")
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>

    @toastr_render
    @livewireScripts
@endpush
