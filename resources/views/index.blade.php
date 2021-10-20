@extends("hexide_admin::master")

@section("title_prefix", trans_choice("models.$module.name", 2))

@php
    /**
     * @var string $module
     * @var array|null $url_params
     * @var bool|null $with_create
     */
@endphp

@section("content_header")
    <div class="row">
        <div class="col-lg-12 d-flex justify-content-between">
            <div class="col-md-6 row">
                @if(($with_create ?? true) && Route::has('admin.'.$module.'.create'))
                    @can(\HexideDigital\ModelPermissions\Models\Permission::key($module, \HexideDigital\ModelPermissions\Models\Permission::create))
                        <a class="btn btn-success" title="{{__('buttons.add')}}"
                           href="{{route('admin.'.$module.'.create', $url_params)}}">

                            <span class="mr-2"><i class="fas fa-plus"></i></span>

                            {{__('buttons.add')}}
                        </a>
                    @endcan
                @endif

                @yield("content_header_add", '')
            </div>

            <div class="col-md-6">
                @includeIf('hexide_admin::partials.breadcrumbs')
            </div>
        </div>
    </div>
@endsection
