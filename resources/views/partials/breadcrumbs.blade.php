@php
    /**
     * @var \Illuminate\Support\Collection $breadcrumbs
     */
@endphp

@if($breadcrumbs->isNotEmpty())
    <ol class="breadcrumb float-sm-right bg-transparent {!! $ol_class ?? '' !!}">
        <li class="breadcrumb-item {{Route::currentRouteNamed('admin.home')?'active':''}}">
            <a href="{!! route('admin.home') !!}">
                <span class=""><i class="fas fa-home"></i></span>
                {{__('admin_labels.dashboards')}}
            </a>
        </li>
        @foreach ($breadcrumbs as $i => $data)
            @if(!$loop->last)
                <li class="breadcrumb-item">
                    <a href="{{$data['url']}}">{{__($data['name'])}}</a>
                </li>
            @else
                <li class="breadcrumb-item active">
                    {{__($data['name'])}}
                </li>
            @endif
        @endforeach
    </ol>
@endif
