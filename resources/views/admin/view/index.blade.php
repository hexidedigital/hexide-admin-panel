@extends('hexide-admin::index', ['with_create' => true])

@section("content")
    <div class="row">
        <div class="col-12">
            @php
                $tableName = \Str::singular(\Str::slug($module)) . '-table';
            @endphp
            @livewire("hexide-admin::admin.tables.".$tableName)
        </div>
    </div>
@endsection
