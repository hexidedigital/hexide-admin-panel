@php
    /**
     * @var string|null $route Custom route for redirect, usually contains a parameter
     * @var \Illuminate\Database\Eloquent\Model $model
     * @var array|null $url_params
     * @var string $module
     */
$url_params = $url_params ?? [];

$url_params_edit = array_merge(
    [ Str::singular($module) => $model->{$model->getKeyName()}]
    , $url_params ?? []
)
@endphp

<div class="col-12 d-flex justify-content-between @if (!empty($class)) {!! $class !!} @endif">
    <div class="col-12 col-sm-8 col-md-6">
        <a class="btn btn-secondary"
           href="{!! !empty($back_url) ? $back_url
              : ( !empty($route) ? $route
                    : route('admin.' . $module . '.index', $url_params)
                  ) !!}">

            <span class="mr-2"><i class="fas fa-chevron-left"></i></span>

            {{__('hexide-admin::buttons.back')}}
        </a>
    </div>

    @if(isset($model->id) && Route::has('admin.'.$module.'.edit'))
        @if(permission_can(\HexideDigital\ModelPermissions\Models\Permission::Edit, $module))
            <div class="col-sm-6 justify-content-end text-right">
                <a class="btn btn-warning" title="{{__('hexide-admin::buttons.edit')}}"
                   href="{{route('admin.'.$module.'.edit', $url_params_edit)}}">

                    <span class="mr-2"><i class="fas fa-pencil-alt"></i></span>

                    {{__('hexide-admin::buttons.edit')}}
                </a>
            </div>
        @endif
    @endisset
</div>
