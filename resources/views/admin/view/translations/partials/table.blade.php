@php
    /**
     * @var array|string[] $locales
     * @var string $locale
     */

$keyColumnWidth = '300px';
@endphp

<div class="card shadow-sm my-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th style="width: {{$keyColumnWidth}}">{{__('admin_labels.attributes.key')}}</th>

                    @foreach($locales as $locale)
                        <th style="width: calc((100% - {{$keyColumnWidth}}) / {{sizeof($locales)}})">{!! __('admin_labels.locales.'.$locale) !!}</th>
                    @endforeach
                </tr>
                </thead>

                <tbody>
                @foreach($list as $key => $item)
                    <tr>
                        <td>
                            <div style="width: {{$keyColumnWidth}}">
                                {{$key}}
                            </div>
                        </td>
                        @foreach($locales as $locale)
                            <td class="form-group {{$errors->first("$locale.$key", 'has-error')}}">
                                <label hidden for="{{md5($locale.$key)}}"></label>
                                <textarea class="form-control input-sm" rows="1"
                                          name="{{$locale}}[{{$key}}]" id="{{md5($locale.$key)}}"
                                >{{Arr::get($item, $locale)}}</textarea>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

