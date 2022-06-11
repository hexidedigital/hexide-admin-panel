@php
    Form::model($model ?? null)
@endphp

<x-adminlte-card :theme="$defaultCardTheme" class="shadow" collapsible maximizable>
    @include('hexide-admin::admin.view.admin_configurations.tabs.general')
</x-adminlte-card>
