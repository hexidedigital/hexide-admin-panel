@props([
    'name',
    'hide'=> true,
    'rawAttributes' => null
])

<div
    x-show="isActive('{{$name}}')" @if($hide) x-cloak @endif
    {{ $attributes }}
    {!! $rawAttributes !!}
>
    {!! $slot !!}
</div>
