@php
    /**
     * @var string|null $route Custom route for redirect, usually contains a parameter
     * @var string $module
     */
$url_params = $url_params ?? [];
@endphp

<div class="col-12 d-flex justify-content-between">
    <div class="col-sm-6">
        <a class="btn btn-secondary"
           href="{!! !empty($back_url) ? $back_url
              : ( !empty($route) ? $route
                    : route('admin.' . $module . '.index', $url_params)
                  ) !!}">
            <span class="mr-2"><i class="fas fa-times"></i></span>
            {{__('hexide-admin::buttons.cancel')}}
        </a>
    </div>

    <div class="col-sm-6 justify-content-end text-right">
        <div class="btn-group" :class="show && 'show'" x-data="{show: false}">
            <button type="submit" name="next_action" value="index" class="btn btn-success">
                <span class="mr-2"><i class="far fa-save"></i></span>
                @if(!empty($next_actions['default']))
                    {{$next_actions['default']['index']}}
                @else
                    {{__('hexide-admin::buttons.save')}}
                @endif
            </button>

            @if(!empty($next_actions['menu']))
                <a class="btn btn-success dropdown-toggle" @click.prevent="show = !show">
                    <span class="sr-only">Toggle Dropdown</span>
                </a>
                <div class="dropdown-menu" :class="show && 'show'" x-show="show"
                     style="position: absolute; transform: translate3d(-5px, 38px, 0px); top: 0; left: 0px; will-change: transform; right: auto; bottom: auto;">
                    @foreach($next_actions['menu'] as $next_action => $title)
                        <button type="submit" name="next_action" value="{{$next_action}}" class="dropdown-item">
                            {{$title}}
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
