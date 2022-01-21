@props(['name', 'hide'=> true])

<div x-show="isActive('{{$name}}')" @if($hide) x-cloak @endif>
    {!! $slot !!}
</div>
