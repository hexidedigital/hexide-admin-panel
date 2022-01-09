@extends("hexide-admin::master")

@php
    /**
     * @var string $page
     * @var string $group
     */
@endphp

@section("content")
    <div class="row">
        <div class="col-12">
            {!! Form::open(['url' => route('admin.translations.update', $group), 'method' => 'post', 'class' => 'without-js-validation' ]) !!}
            {!! Form::hidden('page', $page) !!}

            @include('hexide-admin::admin.view.translations.partials.buttons')

            @include('hexide-admin::admin.view.translations.partials.table')

            @include('hexide-admin::admin.view.translations.partials.pagination')
            @include('hexide-admin::admin.view.translations.partials.buttons')

            {!! Form::close() !!}
        </div>
    </div>
@endsection
