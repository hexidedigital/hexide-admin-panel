@extends("hexide_admin::master")

@php
    $layout_type = $layout_type ?? (isset($model->id) ? 'edit' : 'create');
@endphp

@section("title_prefix", __("models.$module.$layout_type"))

@section("content")

    @yield('form-start', isset($model->id)
        ? Form::model($model, ['route' => ["admin.$module.update", $model->id], 'method' => 'put', 'files' => View::getSection('with_files', false)])
        : Form::open(['route' => "admin.$module.store", 'files' => View::getSection('with_files', false)])
    )

    <div class="row mb-3 pt-2">
        @include("admin.partials.buttons.line__cancel_save")
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <div class="row">
                <span class="card-title mr-sm-3">{{__("models.$module.$layout_type")}}</span>
                <div class="col-12 col-sm text-info">
                    <span class="text-red font-weight-bold">*</span> -
                    <i>{{__('admin_labels.required_fields')}}</i>
                </div>
                <div class="col">
                    @include('admin.partials.breadcrumbs', ['ol_class' => 'mb-0 px-2 py-1'])
                </div>
            </div>
        </div>

        <div class="card-body">

            @yield('form-body')

        </div>
    </div>

    <div class="row mb-3">
        @include("admin.partials.buttons.line__cancel_save")
    </div>

    @yield('form-close', Form::close())

@stop
