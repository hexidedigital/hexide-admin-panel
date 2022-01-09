@php
    /**
     * @var \Illuminate\Database\Eloquent\Collection $admin_configuration_groups
     * @var \HexideDigital\HexideAdmin\Models\AdminConfiguration $admin_configuration
     */
@endphp

{!! Form::model($admin_configuration, ['method' => 'put', 'files' => true, 'id' => 'form_'.$admin_configuration->id,
    'route' => ["admin.admin_configurations.list.update", $admin_configuration->id]]) !!}

{{-- prevent getting old values --}}
{!! Form::hidden('id', $admin_configuration->id) !!}
{!! Form::hidden('type', $admin_configuration->type) !!}
{!! Form::hidden('translatable', $admin_configuration->translatable) !!}

<div class="card card-outline card-navy">
    <div class="card-header">
        <div class="row">
            <div class="col">
                <h3 class="">{{$admin_configuration->name}}</h3>
            </div>
            <div class="">
                @include('hexide-admin::admin.partials.ajax.toggler', ['field' => 'status', 'model' => $admin_configuration])
            </div>
        </div>
        @if($admin_configuration->description)
            <div class="row">
                <div class="col-12">
                    {{$admin_configuration->description}}
                </div>
            </div>
        @endif
    </div>

    <div class="card-body p-0">
        @include("hexide-admin::admin.view.admin_configurations.list.form_fields", ['id' => $admin_configuration->id])
    </div>

    <div class="card-footer d-flex flex-row-reverse">
        <button type="submit" class="btn btn-success btn-sm">
            <span class="mr-2"><i class="far fa-save"></i></span>
            {{__('hexide-admin::buttons.save')}}
        </button>
    </div>
</div>

{!! Form::close() !!}
