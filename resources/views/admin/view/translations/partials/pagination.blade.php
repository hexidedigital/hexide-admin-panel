@php
    /**
     * @var Illuminate\Contracts\Pagination\LengthAwarePaginator $list
     */
@endphp

<div class="row my-2">
    @if ($list->lastPage() > 1)
        <div class="col-12 col-md-8 overflow-auto">
            {{ $list->links() }}
        </div>

        <div class="col-12 col-md-4 text-center text-md-right text-muted">
            <span>@lang('Showing')</span>
            <strong>{{ $list->count() ? $list->firstItem() : 0 }}</strong>
            <span>@lang('to')</span>
            <strong>{{ $list->count() ? $list->lastItem() : 0 }}</strong>
            <span>@lang('of')</span>
            <strong>{{ $list->total() }}</strong>
            <span>@lang('results')</span>
        </div>
    @else
        <div class="col-12 text-muted">
            @lang('Showing')
            <strong>{{ $list->count() }}</strong>
            @lang('results')
        </div>
    @endif
</div>
