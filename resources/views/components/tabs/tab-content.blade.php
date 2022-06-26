@props([
    'name',
    'rawAttributes' => null,
    'transition' => false,
])

<div
    x-show="isActive('{{$name}}')" x-cloak
    {{ $attributes }}
    @if($transition)
        x-transition
    @endif
    {!! $rawAttributes !!}
>
    {!! $slot !!}
</div>
