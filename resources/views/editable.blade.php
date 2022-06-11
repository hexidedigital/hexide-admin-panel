@extends("hexide-admin::master")

@php
    $layout_type = $layout_type ?? (isset($model->id) ? 'edit' : 'create');
@endphp

@section("title_prefix", __("models.$module.$layout_type"))
@section("header_title", __("models.$module.$layout_type"))

@section("content")

    @if(config('hexide-admin.button-lines.edit-forms.top-line.show', false))
        <div class="mb-3">
            @include("hexide-admin::partials.buttons.line__cancel_save")
        </div>
    @endif

    @yield('form-start', isset($model->id)
        /** @warning Form model not working with `yield` */
        ? Form::model($model, ['route' => ["admin.$module.update", $model->id], 'method' => 'put', 'files' => View::getSection('with_files', true)])
        : Form::open(['route' => "admin.$module.store", 'files' => View::getSection('with_files', true)])
    )

{{--    {!! View::getSection('form-body', view("admin.view.$module.partials._form")) !!}--}}
    @yield('form-body')

    @if(config('hexide-admin.button-lines.edit-forms.bottom-line.show', true))
        <div class="mb-3">
            @include("hexide-admin::partials.buttons.line__cancel_save")
        </div>
    @endif

    @yield('form-close', Form::close())
@stop
