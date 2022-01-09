<div class="col-12 col-md-10 col-lg-8 m-auto" x-data="{show: true, body: true}"
     x-show="show" x-transition.duration.300ms>
    <div class="card bg-gradient-warning">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i></h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" @click.prevent="body = !body">
                    <i class="fas fa-lg fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" @click.prevent="show = false">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-body" x-show="body" x-transition.duration.300ms>
            {{__('models.admin_configurations.be_careful_when_saving')}}
        </div>
    </div>
</div>
