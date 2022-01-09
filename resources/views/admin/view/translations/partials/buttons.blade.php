<div class="row justify-content-between my-2">
    <div class="col-md-6 text-left">
        <a href="{{route('admin.translations.index', $group)}}" class="btn btn-secondary">
            {{__('hexide-admin::buttons.cancel')}}
        </a>
    </div>

    <div class="col-md-6 text-right">
        <button class="btn btn-success" type="submit">
            {{__('hexide-admin::buttons.save')}}
        </button>
    </div>
</div>
