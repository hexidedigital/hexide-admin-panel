@php
    /**
     * @var \HexideDigital\HexideAdmin\Http\Livewire\Admin\Tables\TranslationTable $this
     * @var \HexideDigital\HexideAdmin\Classes\DBTranslations\TranslationRow $row
     */
@endphp

@php($rowKey = $row->getKey())

<x-livewire-tables::bs4.table.cell>
    <div style="width: {{$this->keyColumnWidth}};">
        <small>
            {!! $rowKey !!}
        </small>
    </div>
</x-livewire-tables::bs4.table.cell>

@foreach($this->locales as $locale)
    <x-livewire-tables::bs4.table.cell>
        <label hidden for="{{md5($locale.$rowKey)}}"></label>
        <textarea class="form-control input-sm"
                  rows="{{$row->getTranslationItem($locale)->suggestRows(sizeof($this->locales))}}"
                  name="{{$locale}}[{{$rowKey}}]"
                  id="{{md5($locale.$rowKey)}}"
        >{{$row->getTranslationItem($locale)->getValue()}}</textarea>

        @include('hexide-admin::admin.view.translations.partials.additional-info', [
            'translationItem' => $row->getTranslationItem($locale)
        ])
    </x-livewire-tables::bs4.table.cell>
@endforeach
