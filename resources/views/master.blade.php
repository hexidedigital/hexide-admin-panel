@extends('adminlte::page')

@section("title-suffix", config('adminlte.title'))

@push("css")
    <link rel="stylesheet" href="{{asset('/vendor/flag-icon-css/css/flag-icon.min.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
@endpush

@push("js")
    <script>
        window.toggleInitClass = '{{config('hexide-admin.toggle.init_class')}}';
        window.replaseMeKey = '{{config('hexide-admin.replaseme_string')}}';
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"
            integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>

    <script src="{{ mix('/js/admin/alpine.min.js') }}"></script>

    @toastr_render
@endpush

@section("content_top_nav_right")
    <x-hexide-admin-language-item/>
@endsection

@section("content_header")
    <div class="row mb-2">
        <div class="col-sm-6 d-flex align-items-center">
            <h1 class="d-inline">
                @yield("header_title", trans_choice("models.$module.name", 2))
            </h1>
            @stack("content_header_add")
        </div>
        <div class="col-sm-6">
            @include('hexide-admin::partials.breadcrumbs')
        </div>
    </div>
@endsection
