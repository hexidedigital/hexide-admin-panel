@extends("hexide_admin::master")

@section("title_prefix", __("models.$module.show"))

@section("content")

    <div class="row mb-3">
        @include("admin.partials.buttons.line__back_edit")
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <span class="card-title">
                {{__("models.$module.show")}}
            </span>
            <div class="col">
                @include('admin.partials.breadcrumbs', ['ol_class' => 'mb-0 px-2 py-1'])
            </div>
        </div>

        <div class="card-body">
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
        </div>
    </div>

    <div class="row mb-3">
        @include("admin.partials.buttons.line__back_edit")
    </div>

@stop
