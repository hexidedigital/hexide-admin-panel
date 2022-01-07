@props([
'size' => 150,
'src' => null,
 ])

<img src="{{ !empty($src) ? FileUploader::url($src) : asset("/img/800x800.png") }}"
     {!! $attributes !!} style="max-width: {{$size}}px; max-height: {{$size}}px">
