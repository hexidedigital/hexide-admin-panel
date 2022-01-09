@php
    /**
     * @var \App\Models\Permission|null $model
     * @var Illuminate\Support\ViewErrorBag $errors
     */
@endphp

<div class="form-group required">
    {!! Form::label('title', __("admin_labels.attributes.name")) !!}
    {!! Form::text('title', old('title', $model->title ?? ''), ['placeholder' => __("admin_labels.attributes.name"), 'class' => 'form-control form-control-border']) !!}
    {!! $errors->first('title', '<p class="help-block text-red">:message</p>') !!}
</div>

