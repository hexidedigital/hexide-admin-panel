@extends("hexide-admin::editable")

@section("form-start")
    {!! Form::model($model, ['route' => ["admin.$module.update", $model->id], 'method' => 'put', 'files' => true]) !!}
@endsection

@section("form-body")
    @include("hexide-admin::admin.view.$module.partials._form")
@endsection
