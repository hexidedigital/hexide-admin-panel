@php
    /**
     * @var \HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables\TranslationTable $this
     * @var \Illuminate\Support\Collection $row
     */
@endphp

@php($rowKey = $row->get('key'))

<x-livewire-tables::bs4.table.cell>
    <div style="width: {{$this->keyColumnWidth}};">
        {!! $rowKey !!}
    </div>
</x-livewire-tables::bs4.table.cell>

@foreach($this->locales as $locale)
    <x-livewire-tables::bs4.table.cell>
        <label hidden for="{{md5($locale.$rowKey)}}"></label>
        <textarea class="form-control input-sm" rows="1"
                  name="{{$locale}}[{{$rowKey}}]" id="{{md5($locale.$rowKey)}}"
        >{{collect($row->get($locale))->get('value')}}</textarea>

        <div class="row mt-2">
            <small class="col-6 text-center {{collect($row->get($locale))->get('value_from_db') ? 'text-success' : 'text-danger'}}">
                <i class="mr-2 {{collect($row->get($locale))->get('value_from_db') ? 'fas fa-check' : 'fas fa-times'}}"></i>
                Value stored in db
            </small>
            <small class="col-6 text-center {{collect($row->get($locale))->get('exists_in_file') ? 'text-success' : 'text-danger'}}">
                <i class="mr-2 {{collect($row->get($locale))->get('exists_in_file') ? 'fas fa-check' : 'fas fa-times'}}"></i>
                Key exists in file
            </small>
        </div>
    </x-livewire-tables::bs4.table.cell>
@endforeach
