@if($showLanguages())
    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" aria-expanded="false">
            <i class="{{$currentLocaleIcon()}}"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right p-0" style="left: inherit; right: 0;">
            @foreach($locales as $locale)
                <a href="{{route('admin.locale', $locale)}}" @class(["dropdown-item", "active" => $isCurrentLocale($locale)])>
                    <i class="mr-2 {{$localeIcon($locale)}}"></i>

                    {{__('admin_labels.locales.' . $locale, [], $locale)}}
                </a>
            @endforeach
        </div>
    </li>
@endif
