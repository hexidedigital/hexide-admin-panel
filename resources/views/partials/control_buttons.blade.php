@php
    use \Illuminate\Support\Str;
    use \HexideDigital\ModelPermissions\Models\Permission;
    /**
     * @var string $module
     * @var \Illuminate\Database\Eloquent\Model $model
     * @var bool|null $with_show
     * @var bool|null $with_edit
     * @var bool|null $with_delete
     * @var bool|null $with_restore
     * @var bool|null $with_force_delete
     * @var array|null $url_params
     */

    $with_show = $with_show ?? true;
    $with_edit = $with_edit ?? true;
    $with_delete = $with_delete ?? true;
    $with_restore = $with_restore ?? true;
    $with_force_delete = $with_force_delete ?? true;

    $url_params = $url_params ?? [];
    if(isset($model->id)) {
        $url_params = array_merge(
            [ Str::singular($module) => $model->{$model->getKeyName()}]
            , $url_params
        );
    }
@endphp

@isset($module)
    <div class="d-flex flex-nowrap">

        @if($with_show && Route::has("admin.$module.show") && empty($model->deleted_at))
            @if(permission_can(Permission::View, $module))
                @include('hexide-admin::partials.buttons.control_button',
                    [ "href" => route("admin.$module.show", $url_params), "title" => __("hexide-admin::buttons.show"), "color" => "indigo", "icon" => "fas fa-eye"]
                )
            @endif
        @endif

        @if($with_edit && Route::has("admin.$module.edit") && empty($model->deleted_at))
            @if(permission_can(Permission::Edit, $module, $model))
                @include('hexide-admin::partials.buttons.control_button',
                    [ "href" => route("admin.$module.edit", $url_params), "title" => __("hexide-admin::buttons.edit"), "color" => "warning", "icon" => "fas fa-pencil-alt"]
                )
            @endif
        @endif

        @if($with_delete && Route::has("admin.$module.destroy") && empty($model->deleted_at))
            @if(permission_can(Permission::Delete, $module, $model))
                <form action="{{ route("admin.$module.destroy", $model->{$model->getKeyName()}) }}"
                      method="POST" style="display: inline-block;"
                      onsubmit="return confirm('{{__("hexide-admin::messages.confirm.delete")}}');">
                    @csrf
                    @method("DELETE")

                    @include('hexide-admin::partials.buttons.control_button',
                        [ "type" => "submit", "title" => __("hexide-admin::buttons.delete"), "color" => "danger", "icon" => "fas fa-trash",]
                    )
                </form>
            @endif
        @endif


        @if($with_force_delete && Route::has("admin.$module.restore") && !empty($model->deleted_at))
            @if(permission_can(Permission::Restore, $module, $model))
                <form action="{{ route("admin.$module.restore", $model->{$model->getKeyName()}) }}"
                      method="POST" style="display: inline-block;"
                      onsubmit="return confirm('{{__("hexide-admin::messages.confirm.restore")}}');">
                    @csrf
                    @method("PUT")

                    @include('hexide-admin::partials.buttons.control_button',
                        [ "type" => "submit", "title" => __("hexide-admin::buttons.restore"), "color" => "success", "icon" => "fas fa-trash-restore",]
                    )
                </form>
            @endif
        @endif

        @if($with_restore && Route::has("admin.$module.forceDelete") && !empty($model->deleted_at))
            @if(permission_can(Permission::ForceDelete, $module, $model))
                <form action="{{ route("admin.$module.forceDelete", $model->{$model->getKeyName()}) }}"
                      method="POST" style="display: inline-block;"
                      onsubmit="return confirm('{{__("hexide-admin::messages.confirm.force_delete")}}');">
                    @csrf
                    @method("DELETE")

                    @include('hexide-admin::partials.buttons.control_button',
                        [ "type" => "submit", "title" => __("hexide-admin::buttons.forceDelete"), "color" => "danger", "icon" => "fas fa-eraser",]
                    )
                </form>
            @endif
        @endif

    </div>
@endif
