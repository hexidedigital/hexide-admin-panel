@extends("hexide-admin::editable")

@section("title_prefix", __("models.profile"))
@section("header_title", $model->name)

@section("form-start")
    {!! Form::model($model, ['route' => ["admin.$module.update", ['user' => $model->id, 'from_profile' => 1]], 'method' => 'put', 'files' => true]) !!}
@endsection

@section("form-body")
    <div class="row">
        <div class="col-md-6">
            <x-adminlte-card theme="navy" class="" :title="__('admin_labels.tab_general')" collapsible>
                @include("hexide-admin::admin.view.$module.tabs.general")
            </x-adminlte-card>
        </div>
        <div class="col-md-6 pb-3">
            <x-adminlte-card theme="primary" class="" :title="__('models.users.attributes.password')" collapsible>
                @include("hexide-admin::admin.view.$module.tabs.passwords")
            </x-adminlte-card>
        </div>
    </div>
@endsection
