@props([
    'tabItems' => null,
    'tabContents' => null,
])

@php
    /**
     * @var Illuminate\Support\ViewErrorBag $errors
     */
@endphp

{{--<div @if($showTabPanel) x-data='TabItems' @endif>--}}
<x-slot name="slotTabs">
    <ul
        @class(["nav nav-tabs border-bottom-0", "d-none" => !$showTabPanel])
        role="tablist"
    >

        @if($showLocaleTabs)
            @foreach ($locales as $locale)
                <x-hexide-admin::tabs.tab-link
                    :name="$locale"
                    :icon="'flag-icon flag-icon-'.$getIconForLocale($locale)"
                    :text='__("admin_labels.locales.$locale")'
                    :has-errors="$errors->has($locale.'.*')"
                />
            @endforeach
        @endif

        @if($showGeneralTab)
            <x-hexide-admin::tabs.tab-link
                name="general"
                :text="__('admin_labels.tab_general')"
                :hasErrors="$errors->getBag('default')->hasAny($generalTabErrors)"
            />
        @endif

        {!! $tabItems !!}
    </ul>
</x-slot>

<div @class(["tab-content" => $showTabPanel])>

    @if($showLocaleTabs)
        @foreach ($locales as $locale)
            <x-hexide-admin::tabs.tab-content :name="$locale">
                @include($localeView(), ['locale' => $locale])
            </x-hexide-admin::tabs.tab-content>
        @endforeach
    @endif

    @if($showGeneralTab)
        <x-hexide-admin::tabs.tab-content name="general" :hide="$showTabPanel">
            @include($generalView())
        </x-hexide-admin::tabs.tab-content>
    @endif

    {!! $tabContents !!}

</div>
{{--</div>--}}
