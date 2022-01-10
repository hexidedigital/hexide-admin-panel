@extends('hexide-admin::index', ['with_create' => true])

@section("content")
    <div class="row">
        <div class="col-12">
            @livewire("hexide-admin::admin.tables.configuration-table")
        </div>
    </div>
@endsection
