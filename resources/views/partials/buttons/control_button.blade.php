@php
    $color = $color ?? 'navy';
    $title = $title ?? '';
    $href = $href ?? '#';
    $icon = $icon ?? '';
    $type = $type ?? 'link'
@endphp

@if($type == 'link')
    <a class="btn btn-lg ml-2 mr-4 p-0" title="{{$title}}" href="{{$href}}">
        <span class="text-{{$color}}">
            <i class="{{$icon}}"></i>
            @if(empty($icon)) {{$title}} @endif
        </span>
    </a>
@elseif($type == 'button' || $type == 'submit')
    <button class="btn btn-lg ml-2 mr-4 p-0" title="{{$title}}" href="{{$href}}" @if($type == 'submit') type="submit" @endif>
        <span class="text-{{$color}}">
            <i class="{{$icon}}"></i>
            @if(empty($icon)) {{$title}} @endif
        </span>
    </button>
@endif
