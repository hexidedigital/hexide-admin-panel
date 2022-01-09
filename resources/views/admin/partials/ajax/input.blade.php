@php
    /**
     * @var \Illuminate\Database\Eloquent\Model $model
     * @var string $field
     * @var string $module
     * @var string $type
     */
@endphp

<input type="{!! $type !!}" class="ajax_input form-control" style="max-width: 100px"
       value="{!! $model->{$field} !!}"
       data-id="{!! $model->{$model->getKeyName()} !!}"
       data-token="{!! csrf_token() !!}"
       data-field="{!! $field !!}"
       data-url="{!! route('admin.' . $module . '.ajax_field', ['id' => $model->{$model->getKeyName()}]) !!}"
       data-value="{!! $model->{$field} !!}"/>
