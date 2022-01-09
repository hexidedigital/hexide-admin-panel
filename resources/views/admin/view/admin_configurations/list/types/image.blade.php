<div x-data="imagePreview('{{fu_url($inputValue ?? '')}}')">
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
