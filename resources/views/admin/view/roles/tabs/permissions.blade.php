@php
    /**
     * @var \HexideDigital\ModelPermissions\Models\Role|null $model
     * @var \HexideDigital\ModelPermissions\Models\Permission[]|\Illuminate\Database\Eloquent\Collection|null $permissions
     */
@endphp

<div class="form-group" x-data="{
    selected: [ {{ isset($model->id) ? $model->permissions()->pluck('id')->implode(',') : '' }} ],
    available: '{{ $permissions->toJson() }}',
    get options() {
        return JSON.parse(this.available)
    },
    selectAll() {
        this.selected = Object.keys(this.options);
    },
    deselectAll() {
        this.selected = [];
    },
    isSelected(id) {
        return this.selected.includes(id);
    },
    toggle(id) {
        let index = this.selected.indexOf(id);
        if (index === -1) { this.selected.push(id);}
        else { this.selected.splice(index, 1);}
    },
    pushGroup(ids) {
        ids.forEach((id) => {
            let index = this.selected.indexOf(id);
            if (index === -1) { this.selected.push(id);}
        })
    },
    removeGroup(ids) {
        ids.forEach((id) => {
            this.selected = this.selected.filter((perm)=>id!==perm)
        })
    }

 }">

    <div class="mb-3">
        <span class="btn btn-info btn-xs" @click="selectAll">
            {{__('hexide-admin::buttons.select_all')}}
        </span>
        <span class="btn btn-info btn-xs" @click="deselectAll">
            {{__('hexide-admin::buttons.deselect_all')}}
        </span>
    </div>

    <div class="row">
        @foreach($modules as $module => $permissions)
            <div class="col-6 mb-2">
                <x-adminlte-card theme="navy" :title="trans_choice('models.'.$module.'.name',2)" collapsible
                body-class="p-1">
                    <div class="list-group list-group-flush">
                        <a class="btn btn-sm btn-info"
                           @click="toggleGroup()"
                           x-data="{ add: false, group: [{{$permissions->pluck('id')->implode(',')}}],
                            toggleGroup() { if(this.add) this.pushGroup(this.group); else this.removeGroup(this.group); this.add = ! this.add; } }"
                        >
                            {{$module}} - toggle all
                        </a>
                        @foreach($permissions as $permission)
                            <a class="px-3 py-1 border-top"
                               @click.="toggle({{$permission->id}})"
                            >
                                <input type="checkbox" name="permissions[]" id="permission[{{$permission->id}}]"
                                       x-model="selected" @click.stop
                                       value="{{$permission->id}}">
                                <label for="permission[{{$permission->id}}]">{{$permission->title}}</label>
                            </a>
                        @endforeach
                    </div>
                </x-adminlte-card>
            </div>
        @endforeach
    </div>
</div>
