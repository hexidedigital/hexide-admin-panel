@php
    /**
     * @var \Illuminate\Database\Eloquent\Model $model
     * @var string $field
     * @var string $module
     * @var string $type
     */
@endphp

@php
$route = Route::has("admin.ajax_field.$module")
    ? route("admin.ajax_field.$module", ['id' => $model->getKey()])
    : route("admin.ajax-field.module-name", ['id' => $model->getKey(), 'module_name' => $module])

@endphp

<label>
    <input
        type="{!! $type !!}"
        class="ajax_input form-control {{$module.$model->getKey().$field}}"
        style="max-width: 100px"
        value="{!! $model->getAttribute($field) !!}"
        data-id="{!! $model->getKey() !!}"
        data-token="{!! csrf_token() !!}"
        data-field="{!! $field !!}"
        data-url="{!! $route !!}"
        data-value="{!! $model->getAttribute($field) !!}"
    />
</label>
