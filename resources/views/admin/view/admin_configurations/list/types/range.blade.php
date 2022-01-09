<div class="form-group">
    <input type="text" class="form-control input-sm"
           placeholder="{{__('admin_labels.from')}}"
           name="{{$nameInputCase.'[from]'}}" id="{{$nameDotCase.'.from'}}"
           value="{{array_get(array_wrap($inputValue), 'from')}}">
</div>

<div class="form-group">
    <input type="text" class="form-control input-sm"
           placeholder="{{__('admin_labels.to')}}"
           name="{{$nameInputCase.'[to]'}}" id="{{$nameDotCase.'.to'}}"
           value="{{ array_get(array_wrap($inputValue), 'to')}}">
</div>
