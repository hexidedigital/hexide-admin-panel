@php
    $nameInputCase = $nameInputCase . '[]';
@endphp

<select class="form-control select2 input-sm" aria-hidden="true" multiple
        name="{{$nameInputCase}}" id="{{$nameDotCase}}">
    @foreach($values ?? [] as $value => $title)
        <option value="{{$value}}" @if($inputValue && in_array($value, $inputValue)) selected @endif>
            {{$title}}
        </option>
    @endforeach
</select>
