@prepend('after-styles')
<style>
    input[type="date"] {
        display: inline-block;
        position: relative;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        background: transparent;
        bottom: 0;
        color: transparent;
        cursor: pointer;
        height: auto;
        left: 0;
        position: absolute;
        right: 0;
        top: 0;
        width: auto;
    }

    #bobotContainer {
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
    #bobotContainer span {
        display: block;
        background:#EEEFF1;
        height: 52px;
        padding-top: 10px;
        width: 155px;
        text-align: center;
        color: #1f1f1f;
        text-decoration: none;
    }
</style>
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Timeline <i class="fas fa-refresh refresh-page" onclick="renderView(`{!!route('timeline.edit', base64_encode($data['timeline']->transactionnumber))!!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Master Project</a></li>
                        <li class="breadcrumb-item spa_route" aria-current="page"><a href="javascript:void(0)" onclick="renderView(`{!!route('master-project.timeline')!!}`)">Timeline</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section row">
        <div class="card">
            <div class="card-body">
                <form class="form form-vertical" id="edit-timeline" action="javascript:void(0)">
                    <input type="hidden" name="timeline_id" class="form-input" value="{{$data['timeline']->id}}">
                    @method('PUT')
                    @csrf
                    <div class="form-body">

                        {{-- HEADER --}}
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="tn_number">Transaction Number</label>
                                    <input type="text" name="default_transactionnumber" id="tn_number" class="form-input form-control" value="{{$data['timeline']->transactionnumber}}" readonly>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="project_show">Project Name</label>
                                    <div id="currentProject">
                                        <div class="row">
                                            <div class="col-12 col-md-11">
                                                <input type="text" name="project_show" id="project_show" class="form-input form-control" value="{{$data['project']->name}} ({{$data['project']->code}})" readonly>
                                                <input type="hidden" name="project_id" class="form-input" value="{{$data['timeline']->project_id}}">
                                            </div>

                                            <div class="col-12 col-md-1">
                                                <a href='javascript:void(0)' onclick="changeProject()" class='btn icon btn-sm btn-outline-warning rounded-pill'>
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="changeProject" style="display: none">
                                        <div class="row">
                                            <div class="col-12 col-md-11">
                                                <select name="new_project_id" id="" class="form-control form-select form-input select2" style="width: 100%">
                                                    <option value=""></option>
                                                    @foreach ($data['all_projects'] as $item)
                                                        <option value="{{$item->id}}">{{$item->name}} ({{$item->code}})</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-12 col-md-1">
                                                <a href='javascript:void(0)' onclick="changeProject()" class='btn icon btn-sm btn-outline-danger rounded-pill'>
                                                    <i class="fas fa-close"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- DETAIL --}}
                        <div id="phase-container">
                            @include('module.transaction.timeline.formEdit')
                        </div>

                        <div class="col-12 d-flex justify-content-end">
                            <button type="submit" id="btn-update" class="btn btn-primary me-1 mb-1">Submit</button>
                            <button type="button" class="btn btn-warning me-1 mb-1" onclick="renderView(`{!!route('master-project.timeline')!!}`)" >Cancel</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
        <div id="bobotContainer">
			<span>Total Bobot : <b id="bobotTotal">{{$data['totalBobot']}}</b></span>
		</div>
    </section>

    @prepend('after-script')
    <script type="text/javascript" src="{{ asset('js/module/transaction/timeline/timeline.js') }}"></script>
</div>
