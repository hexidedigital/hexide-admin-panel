@php
    $theme = $theme ?? 'info'
@endphp

@isset($list)
    @foreach($list as $value)
        <span class="badge badge-{{$theme}}">{{ $value }}</span>
    @endforeach
@endisset
