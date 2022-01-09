@php
    Form::model($model ?? null)
@endphp

<x-adminlte-card theme="navy" class="shadow" collapsible maximizable>
    @include('ahexide-admin::dmin.view.admin_configurations.tabs.general')
</x-adminlte-card>
