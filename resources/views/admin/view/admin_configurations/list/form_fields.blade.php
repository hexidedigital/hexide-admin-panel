@php
    /**
     * @var array $locales
     * @var string $locale
     * @var int $id $admin_configuration->id
     * @var \HexideDigital\HexideAdmin\Models\AdminConfiguration $admin_configuration
     */
@endphp

<div class="mb-3">
    @if(!$admin_configuration->translatable)
        <div class="p-2 px-3">
            @include("hexide-admin::admin.view.admin_configurations.list._field")
        </div>
    @else
        <ul class="nav nav-tabs" role="tablist">
            @foreach ($locales as $locale)
                <li class="nav-item">
                    <a class="nav-link @if($loop->first) active @endif" data-toggle="pill"
                       href="#tab_{{$id}}_{{$locale}}" role="tab"
                       aria-controls="tab_{{$id}}_{{$locale}}"
                       aria-selected="{{$loop->first?'true':'false'}}"
                    >
                        {{__("admin_labels.locales.$locale")}}
                        @if ($errors->has($admin_configuration->id.'.'.$locale.'.*'))
                            <span class="ml-3 text-red">*</span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content p-2 px-3">
            @foreach ($locales as $locale)
                <div class="tab-pane fade @if($loop->first) active show @endif"
                     id="tab_{{$id}}_{{$locale}}" role="tabpanel"
                     aria-labelledby="tab_{{$id}}_{{$locale}}"
                >
                    <div class="form-group">
                        @include("hexide-admin::admin.view.admin_configurations.list._field", ['locale' => $locale])
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
