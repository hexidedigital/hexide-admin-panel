@php
    use \Illuminate\Support\Str;
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

    $with_show = ($with_show ?? true) && Route::has("admin.$module.show") && Gate::allows(\HexideDigital\ModelPermissions\Models\Permission::View, $model);
    $with_edit = ($with_edit ?? true) && Route::has("admin.$module.edit") && Gate::allows(\HexideDigital\ModelPermissions\Models\Permission::Update, $model);
    $with_delete = ($with_delete ?? true) && Route::has("admin.$module.destroy") && Gate::allows(\HexideDigital\ModelPermissions\Models\Permission::Delete, $model);
    $with_restore = ($with_restore ?? true) && Route::has("admin.$module.restore") && Gate::allows(\HexideDigital\ModelPermissions\Models\Permission::Restore, $model);
    $with_force_delete = ($with_force_delete ?? true) && Route::has("admin.$module.forceDelete") && Gate::allows(\HexideDigital\ModelPermissions\Models\Permission::ForceDelete, $model);
    $url_params = $url_params ?? [];
    if(isset($model->id)) {
        $url_params = array_merge(
            [ Str::singular($module) => $model->{$model->getKeyName()}]
            , $url_params
        );
    }
@endphp

@if(isset($module) && isset($model))
    <div class="d-flex flex-nowrap">

        @if(empty($model->deleted_at))

            @if($with_show)

                @include('hexide-admin::partials.buttons.control_button', [
                    "href" => route("admin.$module.show", $url_params), "title" => __("hexide-admin::buttons.show"),
                    "color" => "indigo", "icon" => "fas fa-eye"
                ])
            @endif

            @if($with_edit)
                @include('hexide-admin::partials.buttons.control_button', [
                    "href" => route("admin.$module.edit", $url_params), "title" => __("hexide-admin::buttons.edit"),
                     "color" => "warning", "icon" => "fas fa-pencil-alt"
                ])
            @endif

            @if($with_delete)
                <form action="{{ route("admin.$module.destroy", $model->{$model->getKeyName()}) }}"
                      method="POST" style="display: inline-block;"
                      onsubmit="return confirm('{{__("hexide-admin::messages.confirm.delete")}}');">
                    @csrf
                    @method("DELETE")

                    @include('hexide-admin::partials.buttons.control_button', [
                        "type" => "submit", "title" => __("hexide-admin::buttons.delete"),
                        "color" => "danger", "icon" => "fas fa-trash",
                    ])
                </form>
            @endif

        @else

            @if($with_restore)
                <form action="{{ route("admin.$module.restore", $model->{$model->getKeyName()}) }}"
                      method="POST" style="display: inline-block;"
                      onsubmit="return confirm('{{__("hexide-admin::messages.confirm.restore")}}');">
                    @csrf
                    @method("PUT")

                    @include('hexide-admin::partials.buttons.control_button', [
                        "type" => "submit", "title" => __("hexide-admin::buttons.restore"),
                         "color" => "success", "icon" => "fas fa-trash-restore",
                    ])
                </form>
            @endif

            @if($with_force_delete)
                <form action="{{ route("admin.$module.forceDelete", $model->{$model->getKeyName()}) }}"
                      method="POST" style="display: inline-block;"
                      onsubmit="return confirm('{{__("hexide-admin::messages.confirm.force_delete")}}');">
                    @csrf
                    @method("DELETE")

                    @include('hexide-admin::partials.buttons.control_button', [
                        "type" => "submit", "title" => __("hexide-admin::buttons.forceDelete"),
                        "color" => "danger", "icon" => "fas fa-eraser",
                    ])
                </form>
            @endif
        @endif

    </div>
@endif
