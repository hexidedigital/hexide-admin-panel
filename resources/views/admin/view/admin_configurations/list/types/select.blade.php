<select class="form-control select2 input-sm" aria-hidden="true"
        name="{{$nameInputCase}}" id="{{$nameDotCase}}">
    @foreach($values ?? [] as $value => $title)
        <option value="{{$value}}" @if($inputValue == $value) selected @endif>
            {{$title}}
        </option>
    @endforeach
</select>
