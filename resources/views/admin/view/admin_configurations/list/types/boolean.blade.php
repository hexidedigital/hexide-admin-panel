<select class="form-control input-sm" aria-hidden="true"
        name="{{$nameInputCase}}" id="{{$nameDotCase}}">
    @foreach(['0' => __('admin_labels.status_off'), '1' => __('admin_labels.status_on')] as $value => $title)
        <option value="{{$value}}" @if($inputValue == $value) selected @endif>
        {{$title}}
        </option>
    @endforeach
</select>
