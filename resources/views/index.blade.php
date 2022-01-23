@extends("hexide-admin::master")

@section("title_prefix", trans_choice("models.$module.name", 2))
@section("header_title", trans_choice("models.$module.name", 2))

@php
    /**
     * @var array|null $url_params
     * @var bool|null $with_create
     */
@endphp

@push("content_header_add")
    <div class="ml-3">
        @if(($with_create ?? true) && Route::has('admin.'.$module.'.create') && Gate::allows('create', $model))
            <a class="btn btn-outline-success btn-sm" title="{{__('hexide-admin::buttons.add')}}"
               href="{{route('admin.'.$module.'.create', $url_params)}}">
                <span class="mr-2"><i class="fas fa-plus"></i></span>
                {{__('hexide-admin::buttons.add')}}
            </a>
        @endif
    </div>
@endpush
