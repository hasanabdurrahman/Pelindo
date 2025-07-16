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
                <h3>Add Timeline <i class="fas fa-refresh refresh-page" onclick="renderView(`{!!route('timeline.add', base64_encode($data['project']->id))!!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Master Project</a></li>
                        <li class="breadcrumb-item spa_route" aria-current="page"><a href="javascript:void(0)" onclick="renderView(`{!!route('master-project.timeline')!!}`)">Timeline</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section row">
        <div id="timeline_res_data">

        </div>
        <div class="card">
            <div class="card-body">
                <form class="form form-vertical" id="add-timeline" action="javascript:void(0)">
                    @csrf
                    <input type="hidden" name="project_id" class="form-input" value="{{$data['project']->id}}">
                    <div class="form-body">
                        <div id="choose-timeline-type">
                            <center>
                                <h6>Please Choose Timeline Type</h6>
                                <div class="form-group" style="width:300px">
                                    <select class="form-select form-input required" name="timeline-type" id="timeline-type">
                                        <option value="" selected disabled>--- Choose Timeline Type ---</option>
                                        @foreach ($data['timeline_type'] as $ph)
                                            <option value="{{$ph->id}}">{{$ph->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="btn btn-sm btn-primary" onclick="setPhase()">
                                    Set Phase
                                </button>
                            </center>
                        </div>
                        <div id="phase-container" style="display: none">
                            {{-- @include('module.master-project.timeline.formAdd') --}}
                        </div>

                        <div class="col-12 d-flex justify-content-end" id="button-container">
                            <button type="submit" id="btn-save" class="btn btn-primary me-1 mb-1" style="display: none !important">Submit</button>
                            <button type="button" class="btn btn-warning me-1 mb-1" onclick="cancel()" >Cancel</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
        <div id="bobotContainer" style="display: none">
			<span>Total Bobot : <b id="bobotTotal">0</b></span>
		</div>
    </section>

    @prepend('after-script')
    <script type="text/javascript" src="{{ asset('js/module/transaction/timeline/timeline.js') }}"></script>
</div>
