<div x-data="imagePreview('{{fu_url(array_get(array_wrap($inputValue), 'image') ?: '')}}')">
    <div class="custom-file">
        <input type="file" class="custom-file-input" accept="image/*"
               name="{{$nameInputCase.'[image]'}}" id="{{$nameDotCase}}"
               @change="setPreview($event)">
        <label class="custom-file-label" for="{{$nameDotCase}}" x-text="fileName"></label>
    </div>

    <x-hexide-admin::image class="img-thumbnail mt-3" x-bind:src="getPreview"/>

    <p class="pt-3">
        <button type="button" class="btn btn-warning btn-sm" x-show="image" @click.prevent="removeImage"
                @if(empty($inputValue)) style="display: none" @endif>
            {!! __('hexide-admin::buttons.delete') !!}
        </button>
        <input type="hidden" name="{{$nameInputCase.'[isRemoveImage]'}}" :value="is_image_deleted">
    </p>
</div>

<div class="form-group">
    <input type="text" class="form-control input-sm"
           placeholder="{{__('admin_labels.attributes.link')}}"
           name="{{$nameInputCase.'[url]'}}" id="{{$nameDotCase.'.url'}}"
           value="{{array_get(array_wrap($inputValue), 'url')}}">
</div>


<div class="form-group">
    <input type="text" class="form-control input-sm"
           placeholder="{{__('admin_labels.attributes.title')}}"
           name="{{$nameInputCase.'[title]'}}" id="{{$nameDotCase.'.title'}}"
           value="{{array_get(array_wrap($inputValue), 'title')}}">
</div>


<div class="form-group">
    <textarea class="form-control input-sm"
              placeholder="{{__('admin_labels.attributes.value')}}"
              name="{{$nameInputCase.'[content]'}}" id="{{$nameDotCase.'.content'}}"
    >{{array_get(array_wrap($inputValue), 'content')}}</textarea>
</div>
