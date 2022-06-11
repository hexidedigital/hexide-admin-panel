@php
    /**
     * @var \App\Models\User $model
     */
@endphp

<div class="form-group">
    {!! Form::label('roles', trans_choice("models.roles.name", 2) ) !!}

    <span class="mb-3 pl-3">
        <span class="btn btn-info btn-xs select-all">{{__('hexide-admin::buttons.select_all')}}</span>
        <span class="btn btn-info btn-xs deselect-all">{{__('hexide-admin::buttons.deselect_all')}}</span>
    </span>

    {!! Form::select('roles[]', $roles, isset($model->id) ? $model->roles()->pluck('id') : null, ['multiple' => true, 'class'=>"select2" ]) !!}
</div>
