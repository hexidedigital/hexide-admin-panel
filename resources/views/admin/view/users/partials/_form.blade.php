@section('with_files', true)

@php
    Form::model($model ?? null)
@endphp

<div class="row">
    <div class="col-md-6">
        <x-adminlte-card theme="navy" class="" :title="__('admin_labels.tab_general')" collapsible>
            @include("hexide-admin::admin.view.$module.tabs.general")
        </x-adminlte-card>

        @isRole(\HexideDigital\ModelPermissions\Models\Role::SuperAdmin)
        <x-adminlte-card theme="warning" class="" :title="trans_choice('models.roles.name',2)" collapsible>
            @include("hexide-admin::admin.view.$module.tabs.roles")
        </x-adminlte-card>
        @endif
    </div>
    <div class="col-md-6 pb-3">
        <x-adminlte-card theme="primary" class="" :title="__('models.users.attributes.password')" collapsible>
            @include("hexide-admin::admin.view.$module.tabs.passwords")
        </x-adminlte-card>
    </div>
</div>
