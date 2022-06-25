@php
    /**
     * @var string|null $route Custom route for redirect, usually contains a parameter
     * @var string $module
     */

$url_params = $url_params ?? [];

@endphp

<div class="row justify-content-between">
    <div class="col-sm-6">
        <a class="btn btn-secondary"
           href="{!! $back_url ?? ($route ?? route("admin.$module.index", $url_params)) !!}">
            <span class="mr-2"><i class="fas fa-times"></i></span>
            {{ __('hexide-admin::buttons.cancel') }}
        </a>
    </div>

    <div class="col-sm-6 justify-content-end text-right">
        <div class="btn-group position-relative"
             @isset($next_actions['menu'])
             :class="open && 'show'"
             x-data="{
                open: false,
                toggle() {
                    if (this.open) {
                        return this.close()
                    }
                    this.open = true
                },
                close(focusAfter) {
                    this.open = false
                    focusAfter && focusAfter.focus()
                }
             }"
             @keydown.esc.prevent.stop="close($refs.button)"
             @focusin.window="! $refs.panel.contains($event.target) && close()"
             x-id="['dropdown-button']"
            @endif
        >
            @isset($next_actions['default'])
                <button type="submit" name="next_action" class="btn btn-success"
                        value="{{ array_first(array_keys($next_actions['default'])) }}"
                >
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
                <button class="btn btn-success dropdown-toggle"
                        type="button" role="button"
                        x-ref="button"
                        @click="toggle()"
                        :aria-expanded="open"
                        :aria-controls="$id('dropdown-button')"
                >
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu position-absolute"
                     :class="open && 'show'"
                     x-ref="panel"
                     x-show="open"
                     x-transition.origin.top.left
                     @click.outside="close($refs.button)"
                     :id="$id('dropdown-button')"
                     style="transform: translate3d(0, 0, 0); top: 38px; left: 0; will-change: transform; right: auto; bottom: auto;">
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
