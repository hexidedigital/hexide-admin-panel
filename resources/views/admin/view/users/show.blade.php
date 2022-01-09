@extends("hexide-admin::show")

@php
    /**
     * @var App\Models\User $model
     */
@endphp

@section("table-body")

    <tr>
        <th>{{__("models.$module.attributes.name")}}</th>
        <td>{{ $model->name }}</td>
    </tr>
    <tr>
        <th>{{__("models.$module.attributes.email")}}</th>
        <td>{{ $model->email }}</td>
    </tr>
    <tr>
        <th>{{trans_choice("models.roles.name", 2)}}</th>
        <td><x-admin.badge :list="$model->roles()->pluck('title')"/></td>
    </tr>

@endsection
