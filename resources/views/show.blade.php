@extends("hexide-admin::master")

@section("title_prefix", __("models.$module.show"))
@section("header_title", __("models.$module.show"))

@section("content")

    <x-adminlte-card :title='__("models.$module.show")' theme="navy" class="shadow" maximizable>
        <table class="table table-bordered table-striped">
            <thead>
            <tr class="bg-gradient-info">
                <th class="col-3">{{__('admin_labels.attribute')}}</th>
                <th class="col">{{__('admin_labels.value')}}</th>
            </tr>
            </thead>

            <tbody>

            @yield('table-body')

            </tbody>
        </table>
    </x-adminlte-card>

    <div class="row mb-3">
        @include("admin.partials.buttons.line__back_edit")
    </div>
@stop
