@props(['name', 'text', 'icon' => '', 'hasErrors' => false])

@php
$hasErrors = $hasErrors ?? false;

$text = $text ?? __("admin_labels.tab.$name");
if($text == "admin_labels.tab.$name") $text = $name;

@endphp

<li class="nav-item" x-init="items.push({ name: '{{$name}}', title: '{{$text}}' });">
    <a class="nav-link {{$hasErrors?' text-red text-bold':''}}"
       :class="isActive('{{$name}}') && 'active'"
       @click="setActive('{{$name}}')"
    >
        <span class="">{{$text}}</span>

        @if(!empty($icon))
            <span class="ml-2 {{$icon}}"></span>
        @endif

        @if ($hasErrors)
            <span class="ml-3 text-red text-bold h5">!</span>
        @else
            <span class="h5"></span>
        @endif
    </a>
</li>
