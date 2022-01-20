@php
    /**
     * @var \Illuminate\Database\Eloquent\Model $model
     * @var string $field
     * @var string $module
     * @var string $type
     */
@endphp

@php
    $with_not_set = $with_not_set ?? true;

        $route = route("admin.ajax_field.$module", ['id' => $model->{$model->getKeyName()}]);
@endphp

<select name="{!! $field !!}" class="ajax_input form-control"
        data-id="{!! $model->{$model->getKeyName()} !!}"
        data-token="{!! csrf_token() !!}"
        data-field="{!! $field !!}"
        data-url="{!! $route !!}"
>
    @if($with_not_set)
        <option
            value="" @if(!isset($model->{$field})) selected @endif>
            {{__('admin_labels.not_set')}}
        </option>
    @endif

    @foreach($array as $key => $name)
        <option
            value="{!! $key !!}" @if($key === $model->{$field}) selected @endif>
            {!! $name !!}
        </option>
    @endforeach
</select>
