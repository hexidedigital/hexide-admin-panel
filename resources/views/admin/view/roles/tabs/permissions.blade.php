@php
    /**
     * @var \App\Models\Role|null $model
     * @var \App\Models\Permission[]|\Illuminate\Database\Eloquent\Collection|null $permissions
     */
@endphp

<div class="form-group">
    <div class="mb-3">
        <span class="btn btn-info btn-xs select-all">{{__('hexide-admin::buttons.select_all')}}</span>
        <span class="btn btn-info btn-xs deselect-all">{{__('hexide-admin::buttons.deselect_all')}}</span>
    </div>

    {!! Form::select('permissions[]', $permissions, isset($model->id) ? $model->permissions()->pluck('id') : null, ['multiple' => true, 'class'=>"form-control select2" ]) !!}
</div>
