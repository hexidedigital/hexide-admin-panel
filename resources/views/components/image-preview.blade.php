@props([
'src' => null,
'name' => 'image',
'size' => 150,
'isRemoveName' => 'isRemoveImage',
])

<div class="row" x-data="imagePreview('{{FileUploader::url($src)}}')">
    <div class="col-12">
        <div class="custom-file" style="max-width: 350px;">
            <input type="file" class="custom-file-input" id="{{str_slug($name)}}" name="{{$name}}" accept="image/*"
                   @change="setPreview($event)">
            <label class="custom-file-label" for="{{str_slug($name)}}" x-text="fileName"></label>
        </div>
    </div>

    <div class="col-12">
        <x-hexide-admin::image class="img-thumbnail mt-3" x-bind:src="getPreview" :size="$size"/>
    </div>

    <div class="col-12 pt-3">
        <button type="button" class="btn btn-warning btn-sm"
                x-show="image" @click.prevent="removeImage"
                @if(empty($src)) style="display: none" @endif>
            {!! __('hexide-admin::buttons.delete') !!}
        </button>
        <input type="hidden" name="{{$isRemoveName}}" :value="is_image_deleted">
    </div>
</div>
