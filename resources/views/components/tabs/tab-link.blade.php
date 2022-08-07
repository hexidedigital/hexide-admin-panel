@props([
    'name',
    'text' => null,
    'icon' => '',
    'hasErrors' => null
])

@php
    $hasErrors ??= $errors->has((string)\Str::of($name)->finish('.*'));

    if (empty($text)) {
        $text = trans()->has($textKey == "admin_labels.tab.$name")
            ? trans($textKey)
            : $name;
    }
@endphp

<li @class(["nav-item"])
    x-init="items.push({ name: '{{ $name }}', title: '{{ $text }}' });"
>
    <a
        @class(['nav-link', 'text-red text-bold' => $hasErrors])
        :class="isActive('{{ $name }}') && 'active'"
        @click.prevent="setActive('{{ $name }}')"
        href="#"
        role="tab"
        :aria-selected="isActive('{{$name}}')"
    >
        <span class="">{{ $text }}</span>

        @if(!empty($icon))
            <span class="ml-2 {{ $icon }}"></span>
        @endif

        @if ($hasErrors)
            <span class="ml-3 text-red text-bold h5">!</span>
        @else
            <span class="h5"></span>
        @endif
    </a>
</li>
