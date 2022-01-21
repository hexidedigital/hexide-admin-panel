@php
    /**
     * @var string|null $route Custom route for redirect, usually contains a parameter
     * @var string $module
     */

$url_params = $url_params ?? [];

@endphp

<div class="row col-12 justify-content-between">
    <div class="col-sm-6">
        <a class="btn btn-secondary"
           href="{!! $back_url ?? ($route ?? route("admin.$module.index", $url_params)) !!}">
            <span class="mr-2"><i class="fas fa-times"></i></span>
            {{ __('hexide-admin::buttons.cancel') }}
        </a>
    </div>

    <div class="col-sm-6 justify-content-end text-right">
        <div class="btn-group" :class="show && 'show'" x-data="{show: false}">
            @isset($next_actions['default'])
                <button type="submit" name="next_action" value="{{ array_first(array_keys($next_actions['default'])) }}" class="btn btn-success">
                    <span class="mr-2"><i class="far fa-save"></i></span>
                    {{ array_first($next_actions['default']) }}
                </button>
            @else
                <button type="submit" name="next_action" value="index" class="btn btn-success">
                    <span class="mr-2"><i class="far fa-save"></i></span>
                    {{ __('hexide-admin::buttons.save') }}
                </button>
            @endif

            @isset($next_actions['menu'])
                <a class="btn btn-success dropdown-toggle" @click.prevent="show = !show">
                    <span class="sr-only">Toggle Dropdown</span>
                </a>
                <div class="dropdown-menu" :class="show && 'show'" x-show="show" @click.outside="show = false"
                     style="position: absolute; transform: translate3d(-5px, 38px, 0px); top: 0; left: 0px; will-change: transform; right: auto; bottom: auto;">
                    @foreach($next_actions['menu'] as $nextAction => $title)
                        <button type="submit" name="next_action" value="{{ $nextAction }}" class="dropdown-item">
                            {{ $title }}
                        </button>
                    @endforeach
                </div>
            @endisset
        </div>
    </div>
</div>
