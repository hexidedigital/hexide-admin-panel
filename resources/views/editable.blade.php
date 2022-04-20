@extends("hexide-admin::master")

@php
    $layout_type = $layout_type ?? (isset($model->id) ? 'edit' : 'create');
@endphp

@section("title_prefix", __("models.$module.$layout_type"))
@section("header_title", __("models.$module.$layout_type"))

@section("content")

    @yield('form-start', isset($model->id)
        /** @warning Form model not working with `yield` */
        ? Form::model($model, ['route' => ["admin.$module.update", $model->id], 'method' => 'put', 'files' => View::getSection('with_files', true)])
        : Form::open(['route' => "admin.$module.store", 'files' => View::getSection('with_files', true)])
    )

{{--    {!! View::getSection('form-body', view("admin.view.$module.partials._form")) !!}--}}
    @yield('form-body')

    <div class="row mb-3">
        @include("hexide-admin::partials.buttons.line__cancel_save")
    </div>

    @yield('form-close', Form::close())
@stop
