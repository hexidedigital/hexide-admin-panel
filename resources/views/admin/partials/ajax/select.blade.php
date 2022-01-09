@php
    /**
     * @var \Illuminate\Database\Eloquent\Model $model
     * @var string $field
     * @var string $module
     * @var string $type
     */
$with_not_set = $with_not_set ?? true;
@endphp

<select name="{!! $field !!}" class="ajax_input form-control"
        data-id="{!! $model->{$model->getKeyName()} !!}"
        data-token="{!! csrf_token() !!}"
        data-field="{!! $field !!}"
        data-url="{!! route('admin.' . $module . '.ajax_field', ['id' => $model->{$model->getKeyName()}]) !!}"
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
