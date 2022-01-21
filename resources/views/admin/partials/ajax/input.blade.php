@php
    /**
     * @var \Illuminate\Database\Eloquent\Model $model
     * @var string $field
     * @var string $module
     * @var string $type
     */
@endphp

@php
    $route = route("admin.ajax_field.$module", ['id' => $model->{$model->getKeyName()}]);
@endphp

<input type="{!! $type !!}" class="ajax_input form-control" style="max-width: 100px"
       value="{!! $model->{$field} !!}"
       data-id="{!! $model->{$model->getKeyName()} !!}"
       data-token="{!! csrf_token() !!}"
       data-field="{!! $field !!}"
       data-url="{!! $route !!}"
       data-value="{!! $model->{$field} !!}"/>
