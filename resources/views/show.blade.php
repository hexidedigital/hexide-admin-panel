@extends("hexide-admin::master")

@section("title_prefix", __("models.$module.show"))
@section("header_title", __("models.$module.show"))

@section("content")

    @if(config('hexide-admin.button-lines.view-form.top-line.show'))
        <div class="mb-3">
            @include("hexide-admin::partials.buttons.line__back_edit")
        </div>
    @endif

    <x-adminlte-card :theme="$defaultCardTheme" class="shadow" :title='__("models.$module.show")' maximizable>
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

    @if(config('hexide-admin.button-lines.view-form.bottom-line.show'))
        <div class="mb-3">
            @include("hexide-admin::partials.buttons.line__back_edit")
        </div>
    @endif
@stop
