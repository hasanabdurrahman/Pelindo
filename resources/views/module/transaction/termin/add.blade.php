@prepend('after-styles')
<style>
    #percentageContainer {
    	height: 0px;
        width: 85px;
        position: fixed;
        right: 0;
        top: 50%;
        z-index: 1000;
        transform: rotate(-90deg);
        -webkit-transform: rotate(-90deg);
        -moz-transform: rotate(-90deg);
        -o-transform: rotate(-90deg);
    }
    #percentageContainer span {
        display: block;
        background:#EEEFF1;
        height: 52px;
        padding-top: 10px;
        width: 185px;
        text-align: center;
        color: #1f1f1f;
        text-decoration: none;
    }
</style>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Add Termin  <i class="fas fa-refresh refresh-page" onclick="renderView(`{!!route('termin.add')!!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Transaction</a></li>
                        <li class="breadcrumb-item spa_route" aria-current="page"><a href="javascript:void(0)" onclick="renderView(`{!!route('master-project.termin')!!}`)">Termin</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Termin</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section row">
        <div class="card">
            <div class="card-body">
                <form class="form form-vertical" id="add-termin" action="javascript:void(0)">
                    @csrf
                    <div class="form-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-6 col-md-6">
                                    <label for="project">Pilih Project</label>
                                    <select onchange="changeVal(this)" class="form-select select-project form-input mt-3" name="project_id" id="project_id" style="width: 100%" data-placeholder="Silahkan Pilih Project">
                                        <option></option>
                                        @foreach ($projects as $project)
                                            <option value="{{$project->id}}">{{$project->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="card mt-4" id="project-detail" style="background-color: #8de4ff72; display:none">
                                    <div class="card-body">
                                        <div class="row justify-content-center">
                                            <div class="col-12 col-md-2"> Nama PC </div>
                                            <div class="col-12 col-md-1"> : </div>
                                            <div class="col-12 col-md-9"> <span id="nama_pc"></span> </div>
                                        </div>

                                        <div class="row justify-content-center">
                                            <div class="col-12 col-md-2"> Nama Sales </div>
                                            <div class="col-12 col-md-1"> : </div>
                                            <div class="col-12 col-md-9"> <span id="nama_sales"></span> </div>
                                        </div>

                                        <div class="row justify-content-center">
                                            <div class="col-12 col-md-2"> Value Project </div>
                                            <div class="col-12 col-md-1"> : </div>
                                            <div class="col-12 col-md-9"> <span id="value"></span> </div>
                                        </div>

                                        <div class="row justify-content-center">
                                            <div class="col-12 col-md-2"> Periode Project </div>
                                            <div class="col-12 col-md-1"> : </div>
                                            <div class="col-12 col-md-9"> <span id="project_time"></span> </div>
                                        </div>

                                        <div class="d-flex flex-row-reverse mt-2">
                                            <button class="btn btn-primary btn-sm btn-add">Buat Termin</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card" id="form-add-container" style="display: none">
                                    <div class="card-body" id="form-inner">
                                        {{-- @include('module.master-project.termin.formAdd') --}}
                                    </div>

                                    <div class="d-flex flex-row-reverse">
                                        <button type="submit" id="btn-save" class="btn btn-primary me-1 mb-1">Submit</button>
                                        <button type="button" class="btn btn-warning me-1 mb-1" onclick="renderView(`{!!route('master-project.termin')!!}`)">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div id="percentageContainer" style="display: none">
			<span>Total Persentase : <b id="percentageTotal">0</b></span>
		</div>
    </section>

    @prepend('after-script')
    <script type="text/javascript" src="{{ asset('js/module/transaction/termin/termin.js') }}"></script>
</div>
