@php
    /**
     * @var \App\Models\User|null $model
     */
@endphp

<div x-data="dataPassword()" class="mb-3">
    <div class="form-group {{isset($model->id)?"":"required"}}">
        {!! Form::label('password', __("models.$module.attributes.password") ) !!}
        {!! Form::password('password' , ['class' => 'form-control form-control-border', 'min' => \App\Models\User::password_min_length, 'x-model' => 'password']) !!}
        {!! $errors->first('password', '<p class="help-block text-red">:message</p>') !!}
    </div>

    <div class="form-group {{isset($model->id)?"":"required"}}">
        {!! Form::label('password_confirmation', __("models.$module.attributes.password_confirmation") ) !!}
        {!! Form::password('password_confirmation' , ['class' => 'form-control form-control-border', 'min' => \App\Models\User::password_min_length, 'x-model' => 'confirm']) !!}

        {!! $errors->first('password_confirmation', '<p class="help-block text-red">:message</p>') !!}
        <p class="help-block text-red" x-cloak x-show="!isConfirmed">
            {{__('validation.confirmed', ['attribute' => __('models.users.attributes.password')])}}
        </p>
    </div>

    <div class="row justify-content-between mx-3">
        <div class="">
            <span x-cloak x-show="generated" x-text="password"></span>
        </div>
        <div class="">
            <button class="btn btn-sm btn-info" type="button" @click.prevent="generate">
                {{__('Generate')}}
            </button>
            <button class="btn btn-sm btn-secondary ml-2" type="reset" @click.prevent="reset">
                {{__('Clear')}}
            </button>
        </div>
    </div>
</div>
