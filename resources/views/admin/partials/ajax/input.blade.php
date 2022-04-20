@php
    /**
     * @var \Illuminate\Database\Eloquent\Model $model
     * @var string $field
     * @var string $module
     * @var string $type
     */
@endphp

@php
    $route = route("admin.ajax_field.$module", ['id' => $model->getKey()]);
@endphp

<label for="{{$module.$model->getKey()}}" hidden></label>
<input type="{!! $type !!}" class="ajax_input form-control" style="max-width: 100px"
       id="{{$module.$model->getKey()}}"
       value="{!! $model->getAttribute($field) !!}"
       data-id="{!! $model->getKey() !!}"
       data-token="{!! csrf_token() !!}"
       data-field="{!! $field !!}"
       data-url="{!! $route !!}"
       data-value="{!! $model->getAttribute($field) !!}"/>
