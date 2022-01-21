@php
    /**
     * @var \HexideDigital\HexideAdmin\Models\AdminConfiguration $model
     */
@endphp

<div class="form-group required">
    {!! Form::label('type', __('admin_labels.attributes.type')) !!}
    {!! Form::select('type', $types, null, ['class'=>"form-control form-control-border" ]) !!}
    {!! $errors->first('type', '<p class="help-block text-red">:message</p>') !!}
</div>

<div class="form-group required">
    {!! Form::label('key', __('admin_labels.attributes.key')) !!}
    {!! Form::text('key', null, ['class' => 'form-control form-control-border'.($errors->has('key')?' is-invalid':'')]) !!}
    {!! $errors->first('key', '<p class="help-block text-red">:message</p>') !!}
    @isset($model->id)
        <i class="form-text text-danger small">{{__('models.admin_configurations.be_careful_when_changing')}}</i>
    @endif
</div>

<div class="form-group required">
    {!! Form::label('name', __('admin_labels.attributes.name')) !!}
    {!! Form::text('name', null, ['class' => 'form-control form-control-border'.($errors->has('name')?' is-invalid':'')]) !!}
    {!! $errors->first('name', '<p class="help-block text-red">:message</p>') !!}
</div>

<div class="form-group">
    {!! Form::label('description', __('admin_labels.attributes.description')) !!}
    {!! Form::text('description', null, ['class' => 'form-control form-control-border'.($errors->has('description')?' is-invalid':'')]) !!}
    {!! $errors->first('description', '<p class="help-block text-red">:message</p>') !!}
</div>

<div class="form-group required">
    {!! Form::label('translatable', __('admin_labels.attributes.translatable')) !!}
    {!! Form::select('translatable', ['0' => __('admin_labels.no'), '1' => __('admin_labels.yes')], null, ['class'=>"form-control form-control-border" ]) !!}
    {!! $errors->first('translatable', '<p class="help-block text-red">:message</p>') !!}
</div>

<hr class="col-5 border-primary border-top">

<div class="form-group">
    {!! Form::label('status', __('admin_labels.attributes.status')) !!}
    {!! Form::select('status', ['1' => __('admin_labels.status_on'), '0' => __('admin_labels.status_off'),], null, ['class'=>"form-control form-control-border" ]) !!}
</div>

<div class="form-group">
    {!! Form::label('group', __('admin_labels.attributes.group')) !!}
    {!! Form::text('group', null,['class' => 'form-control form-control-border'.($errors->has('group')?' is-invalid':''), 'list'=>'group_datalist']) !!}
    {!! Form::datalist('group_datalist', $groups ?? []) !!}
    {!! $errors->first('group', '<p class="help-block text-red">:message</p>') !!}
</div>

<div class="form-group">
    {!! Form::label('in_group_position', __('admin_labels.attributes.in_group_position')) !!}
    {!! Form::number('in_group_position', $model->in_group_position??1, ['class' => 'form-control form-control-border'.($errors->has('in_group_position')?' is-invalid':''), 'min' => 1]) !!}
    {!! $errors->first('in_group_position', '<p class="help-block text-red">:message</p>') !!}
</div>
