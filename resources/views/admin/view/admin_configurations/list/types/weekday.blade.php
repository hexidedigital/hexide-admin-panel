@php
    $weekdays = [];

    $data = new \Carbon\Carbon('Monday');
    foreach (range(1, 7) as $day) {
        $weekdays[$day] = $data->dayName;
        $data->addDay();
    };
@endphp

<select class="form-control select2 input-sm" aria-hidden="true"
        name="{{$nameInputCase}}" id="{{$nameDotCase}}">
    @foreach($weekdays as $value => $title)
        <option value="{{$value}}" @if($inputValue == $value) selected @endif>
            {{$title}}
        </option>
    @endforeach
</select>
