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

    $route = route("admin.ajax_field.$module", ['id' => $model->getKey()]);
@endphp

<label>
    <select name="{!! $field !!}" class="ajax_input form-control {{$module.$model->getKey().$field}}"
            data-id="{!! $model->getKey() !!}"
            data-token="{!! csrf_token() !!}"
            data-field="{!! $field !!}"
            data-url="{!! $route !!}"
    >
        @if($with_not_set)
            <option
                value="" @if(empty($model->getAttribute($field))) selected @endif>
                {{__('admin_labels.not_set')}}
            </option>
        @endif

        @foreach($array as $key => $name)
            <option
                value="{!! $key !!}" @if($key === $model->getAttribute($field)) selected @endif>
                {!! $name !!}
            </option>
        @endforeach
    </select>
</label>
