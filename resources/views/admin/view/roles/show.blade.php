@extends("hexide-admin::show")

@php
    /**
     * @var \HexideDigital\ModelPermissions\Models\Role $model
     * @var string $module
     */
@endphp

@section("table-body")

    <tr>
        <th>{{__("admin_labels.attributes.title")}}</th>
        <td>{{ $model->title }}</td>
    </tr>
    <tr>
        <th>{{__("admin_labels.attributes.key")}}</th>
        <td>{{ $model->key }}</td>
    </tr>
    <tr>
        <th>{{trans_choice("models.permissions.name", 2)}}</th>
        {{-- todo export `admin.badge` --}}
        <td><x-admin.badge :list="$model->permissions()->pluck('title')"/></td>
    </tr>

@endsection
