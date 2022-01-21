@extends("hexide-admin::show")

@php
    /**
     * @var HexideDigital\ModelPermissions\Models\Permission $model
     */
@endphp

@section("table-body")

    <tr>
        <th>{{__("admin_labels.attributes.title")}}</th>
        <td>{{ $model->title }}</td>
    </tr>

@endsection
