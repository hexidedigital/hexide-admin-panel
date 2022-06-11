@php
    Form::model($model ?? null)
@endphp

<div class="col-md-6 m-auto">
    <x-adminlte-card :theme="$defaultCardTheme" class="shadow" collapsible maximizable>
        @include("hexide-admin::admin.view.$module.tabs.general")
    </x-adminlte-card>
</div>
