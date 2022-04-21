@php
    /**
     * @var string $module
     * @var \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\SoftDeletes $model
     * @var bool|null $with_show
     * @var bool|null $with_edit
     * @var bool|null $with_delete
     * @var bool|null $with_restore
     * @var bool|null $with_force_delete
     * @var array|null $url_params
     */

    $with_show = ($with_show ?? true) && Route::has("admin.$module.show") && Gate::allows('view', $model);
    $with_edit = ($with_edit ?? true) && Route::has("admin.$module.edit") && Gate::allows('update', $model);
    $with_delete = ($with_delete ?? true) && Route::has("admin.$module.destroy") && Gate::allows('delete', $model);
    $with_restore = ($with_restore ?? true) && Route::has("admin.$module.restore") && Gate::allows('restore', $model);
    $with_force_delete = ($with_force_delete ?? true) && Route::has("admin.$module.forceDelete") && Gate::allows('forceDelete', $model);
    $url_params = $url_params ?? [];
    if($model->exists) {
        $url_params = array_merge(
            [ str_singular($module) => $model->getKey()]
            , $url_params
        );
    }

    $isDeleted =
        in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($model))
        && !empty($model->getAttribute($model->getDeletedAtColumn()));
@endphp

@if(isset($module) && isset($model))
    <div class="d-flex flex-nowrap">

        @if(!$isDeleted)

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
                <form action="{{ route("admin.$module.destroy", $url_params) }}"
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
                <form action="{{ route("admin.$module.restore", $url_params) }}"
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
                <form action="{{ route("admin.$module.forceDelete", $url_params) }}"
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
