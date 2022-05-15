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

<label>
    <input type="checkbox" class="toogle ajax_ckeckbox {{$module.$model->getKey().$field}}"
        data-id="{!! $model->getKey() !!}"
        data-token="{!! csrf_token() !!}"
        data-field="{!! $field !!}"
        data-url="{!! $route !!}"
        data-value="{!! !$model->getAttribute($field) !!}"

        @foreach($data as $item => $val) data-{{$item}}="{{$val}}" @endforeach
        @foreach($attributes as $item => $val) {{$item}}="{{$val}}" @endforeach
        data-width="75"
        data-size="small"

        {!! ($model->getAttribute($field) ? 'checked="checked"' : '') !!}
    />
</label>
