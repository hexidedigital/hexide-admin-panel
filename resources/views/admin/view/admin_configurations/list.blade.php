@extends("hexide-admin::index", ['with_create'=>false])

@php
    /**
     * @var \Illuminate\Database\Eloquent\Collection $admin_configuration_groups
     * @var \HexideDigital\HexideAdmin\Models\AdminConfiguration $admin_configuration
     */
@endphp

@section("content")
    @include("hexide-admin::admin.view.admin_configurations.list.warning")

    <div class="row" x-data="TabItems">
        <div class="col-3 col-md-2">
            <div class="nav flex-column nav-tabs h-100 border-right-0">
                @foreach($admin_configuration_groups ?? [] as $name => $admin_configurations)
                    @php
                        $tab = __("hexide-admin::admin_labels.admin_configuration_tab.$name");
                        if($tab == "hexide-admin::admin_labels.admin_configuration_tab.$name") $tab = $name;
                    @endphp

                    <x-hexide-admin::tabs.tab-link :name="$name" :text="$tab"/>
                @endforeach
            </div>
        </div>

        <div class="col-9 col-sm-10">
            <div class="tab-content">
                @foreach($admin_configuration_groups ?? [] as $name => $admin_configurations)
                    <x-hexide-admin::tabs.tab-content :name="$name">
                        @foreach($admin_configurations as $admin_configuration)
                            @include("hexide-admin::admin.view.admin_configurations.list.card_form")
                        @endforeach
                    </x-hexide-admin::tabs.tab-content>
                @endforeach
            </div>
        </div>
    </div>
@endsection
