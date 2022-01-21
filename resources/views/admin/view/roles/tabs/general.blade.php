@php
    /**
     * @var \HexideDigital\ModelPermissions\Models\Role|null $model
     * @var Illuminate\Support\ViewErrorBag $errors
     */
@endphp

<div class="form-group required">
    {!! Form::label('title', __("admin_labels.attributes.title")) !!}
    {!! Form::text('title', $model->title ?? '', ['placeholder' => __("admin_labels.attributes.title"), 'class' => 'form-control form-control-border']) !!}
    {!! $errors->first('title', '<p class="help-block text-red">:message</p>') !!}
</div>

<div class="form-group required">
    {!! Form::label('admin_access', __('admin_labels.admin_access')) !!}
    {!! Form::select('admin_access', ['0' => __('admin_labels.no'), '1' => __('admin_labels.yes')], $model->admin_access??null, ['class'=>"form-control form-control-border" ]) !!}
    {!! $errors->first('admin_access', '<p class="help-block text-red">:message</p>') !!}
</div>
