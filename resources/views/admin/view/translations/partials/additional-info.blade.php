@php
    /**
     * @var \HexideDigital\HexideAdmin\Classes\DBTranslations\TranslationItem $translationItem
     */
@endphp

@isRole(\HexideDigital\ModelPermissions\Models\Role::SuperAdmin)
<div class="row">
    <small class="col">
        {{$translationItem->isStub() ? 'Item is stub' : ''}}
    </small>
</div>
<div class="row mt-2">
    <small
        class="col text-center text-{{$translationItem->isValueFromDatabase() ? 'success' : 'danger'}}">
        <i class="mr-2 fas fa-{{$translationItem->isValueFromDatabase() ? 'check' : 'times'}}"></i>
        Value stored in db?
    </small>

    <small
        class="col text-center text-{{$translationItem->isValueExistsInFile() ? 'success' : 'danger'}}">
        <i class="mr-2 fas fa-{{$translationItem->isValueExistsInFile() ? 'check' : 'times'}}"></i>
        Key exists in file?
    </small>

    <small
        class="col text-center text-{{$translationItem->isSame() ? 'success' : 'danger'}}">
        <i class="mr-2 fas fa-{{$translationItem->isSame() ? 'check' : 'times'}}"></i>
        DB and file values are same?
    </small>
</div>
@endisRole
