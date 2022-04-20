@php
    /**
     * @var \Illuminate\Database\Eloquent\Model $model
     * @var string $field
     * @var string $module
     */
@endphp

@php
    $data = $data ?? [];
    $attributes = [];

    if($field === 'status') {
        $data = [
            'on' => '<i class="fas fa-eye"></i>',
            'off' => '<i class="fas fa-eye-slash"></i>',
            'onstyle' => 'success',
            'offstyle' => 'secondary',
        ];
    }

    if(isset($toggle_attributes[$field]) && empty($data)){
        $attributes = $toggle_attributes[$field];
    }

    $route = route("admin.ajax_field.$module", ['id' => $model->getKey()]);
@endphp

<label for="{{$module.$model->getKey()}}" hidden></label>
<input type="checkbox" class="toogle ajax_ckeckbox"
       id="{{$module.$model->getKey()}}"
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
