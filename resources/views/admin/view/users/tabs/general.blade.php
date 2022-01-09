@php
    /**
     * @var \App\Models\User|null $model
     */
@endphp

<div class="form-group required">
    {!! Form::label('name', __("models.$module.attributes.name")) !!}
    {!! Form::text('name', $model->name??null, ['placeholder' => __("models.$module.attributes.name"), 'class' => 'form-control form-control-border']) !!}
    {!! $errors->first('name', '<p class="help-block text-red">:message</p>') !!}
</div>

<div class="form-group required">
    {!! Form::label('email', __("models.$module.attributes.email") ) !!}
    {!! Form::text('email',  $model->email??null, ['placeholder' => __("models.$module.attributes.email"), 'class' => 'form-control form-control-border']) !!}
    {!! $errors->first('email', '<p class="help-block text-red">:message</p>') !!}
</div>
