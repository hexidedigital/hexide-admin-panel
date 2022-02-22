@extends("hexide-admin::index")

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

            <livewire:hexide-admin::admin.tables.translation-table/>

            @include('hexide-admin::admin.view.translations.partials.buttons')

            {!! Form::close() !!}
        </div>
    </div>
@endsection
