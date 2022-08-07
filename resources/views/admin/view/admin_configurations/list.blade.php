@extends("hexide-admin::index", ['with_create' => false])

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
                @foreach($admin_configuration_groups ?? [] as $groupName => $admin_configurations)
                    @php
                        $tabText = trans()->has($tabText = "admin_labels.admin_configuration_tab.$groupName")
                            ? trans($tabText)
                            : $groupName
                    @endphp

                    <x-hexide-admin::tabs.tab-link :name="$groupName" :text="$tabText"/>
                @endforeach
            </div>
        </div>

        <div class="col-9 col-sm-10">
            <div class="tab-content">
                @foreach($admin_configuration_groups ?? [] as $groupName => $admin_configurations)
                    <x-hexide-admin::tabs.tab-content :name="$groupName">
                        @foreach($admin_configurations as $admin_configuration)
                            @include("hexide-admin::admin.view.admin_configurations.list.card_form")
                        @endforeach
                    </x-hexide-admin::tabs.tab-content>
                @endforeach
            </div>
        </div>
    </div>
@endsection
