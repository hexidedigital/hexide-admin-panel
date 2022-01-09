@php
    /**
     * @var \Illuminate\Database\Eloquent\Model $model
     * @var string $field
     * @var string $module
     */

$data = $data??[];
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
@endphp

<input type="checkbox" class="toogle ajax_ckeckbox"
       data-id="{!! $model->{$model->getKeyName()} !!}"
       data-token="{!! csrf_token() !!}"
       data-field="{!! $field !!}"
       data-url="{!! route('admin.' . $module . '.ajax_field', ['id' => $model->{$model->getKeyName()}]) !!}"
       data-value="{!! $model->{$field} == true ? false : true !!}"

       @foreach($data??[] as $item => $val)
       data-{{$item}}="{{$val}}"
       @endforeach
       @foreach($attributes??[] as $item => $val)
       {{$item}}="{{$val}}"
       @endforeach
       data-width="75"
       data-size="small"

    {!! ($model->{$field} ? 'checked="checked"' : '') !!}
/>
