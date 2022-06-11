@php
    Form::model($model ?? null)
@endphp

<div class="row">
    <div class="col-md-4">
        <x-adminlte-card :theme="$defaultCardTheme" class="" :title="__('admin_labels.tab_general')" collapsible>
            @include("hexide-admin::admin.view.$module.tabs.general")
        </x-adminlte-card>
    </div>
    <div class="col-md-8">
        <x-adminlte-card theme="warning" class="" :title="trans_choice('models.permissions.name',2)" collapsible>
            @include("hexide-admin::admin.view.$module.tabs.permissions")
        </x-adminlte-card>
    </div>
</div>
