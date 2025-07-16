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
                <h3>Add Additional Timeline <i class="fas fa-refresh refresh-page" onclick="renderView(`{!!route('additional-timeline.add', base64_encode($data['project']->id))!!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Transaction</a></li>
                        <li class="breadcrumb-item spa_route" aria-current="page"><a href="javascript:void(0)" onclick="renderView(`{!!route('master-project.additional-timeline')!!}`)">Additional Timeline</a></li>
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
                    <input type="hidden" name="transactionnumber" class="form-input" value="{{$data['timeline']->transactionnumber}}">
                    <div class="form-body">
                        <div id="choose-timeline-type" style="display: none">
                            <center>
                                <h6>Please Choose Timeline Type</h6>
                                <div class="form-group" style="width:300px">
                                    <select class="form-select form-input" name="timeline-type" id="timeline-type">
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
                        {{-- HEADER --}}
                        {{-- <div class="row">
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
                        </div> --}}

                        {{-- DETAIL --}}
                        <div id="phase-container">
                            <div class="my-5">
                                <div class="d-flex flex-row-reverse mb-3">
                                    <button class="btn icon icon-left btn-outline-secondary btn-sm rounded-pill" onclick="addPhase('{{$data['project']->id}}')">
                                        <i class="fas fa-add"></i>
                                        Add Phase
                                    </button>
                                </div>

                                @foreach ($data['phase'] as $phase => $items)
                                    <div class="form-group row align-items-center">
                                        <div class="col-md-1 col-12 deletePhase" @if(isset($data['renderFromController'])) @if(!$data['renderFromController']) style="display: none" @endif @else style="display:none" @endif>
                                            <a href='javascript:void(0)' onclick="removePhase(this)" class='btn icon btn-sm btn-outline-danger rounded-pill'>
                                                <i class="bi bi-trash-fill"></i>
                                            </a>
                                        </div>
                                        <div class="col-md-2 col-12">
                                            <label class="col-form-label">Phase Name</label>
                                        </div>
                                        <div class="col-md-9 col-12">
                                            <input type="text" id="phase" class="form-control required phase" name="show_phase[]" value="{{$phase}}" placeholder="Phase Name" onchange="changeVal(this)" onkeydown="changeVal(this)" >
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-12 mt-2">
                                        <div class="table-responsive table-card px-3">
                                            <table class="table table-centered align-middle table-borderless table-nowrap mb-0 data-table" id="example" style="width: 100%">
                                                <thead class="text-muted table-light">
                                                    <tr>

                                                        <th class="text-center" style="width: 200px">Pekerjaan</th>
                                                        <th class="text-center" style="width: 50px">Start Date</th>
                                                        <th class="text-center" style="width: 50px">End Date</th>
                                                        <th class="text-center" style="width: 100px">Bobot</th>
                                                        <th class="text-center" style="width: 200px">Person</th>
                                                        {{-- <th class="text-center" style="width: 200px">Deskripsi</th> --}}
                                                        <th scope="text-center" style="width: 150px">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($items as $key => $item)
                                                    @php
                                                        $phaseNew = str_replace(' ', '_', $phase)
                                                    @endphp
                                                        <tr>
                                                            <td class="form-group">
                                                                <input type="hidden" class="form-input timelineA_id" name="timelineA_id[]" value="{{$item['timelineA_id']}}">
                                                                <input type="hidden" id="phase-hidden" class="form-input required" name="phase[]" value="{{$phase}}">
                                                                <input onchange="changeVal(this)" onkeydown="changeVal(this)" type="text" id="work" class="form-input form-control required" name="work[]" placeholder="Work Name" value="{{$item['task']}}">
                                                            </td>

                                                            <td class="form-group">
                                                                <input onchange="changeVal(this)" onkeydown="return false" min="{{$data['project']->startdate}}" type="date" id="start_date" class="form-input form-control required" name="start_date[]" placeholder="Start Date" value="{{$item['startdate']}}">
                                                            </td>

                                                            <td class="form-group">
                                                                <input onchange="changeVal(this)" onkeydown="return false" max="{{$data['project']->enddate}}" type="date" id="end_date" class="form-input form-control required" name="end_date[]" placeholder="End Date" value="{{$item['enddate']}}">
                                                            </td>

                                                            <td class="form-group">
                                                                <input onchange="changeVal(this)" onkeydown="changeVal(this)" onblur="countBobot(this)" type="text" id="bobot" max="10" class="form-input form-control required" name="bobot[]" placeholder="Bobot" value="{{$item['bobot']}}">
                                                            </td>

                                                            <td class="form-group">
                                                                <select onchange="changeVal(this)" class="employee-{{$phaseNew}}-{{$key}} form-select select-employee form-input required" multiple="multiple" name="employee[]" style="width: 100%">
                                                                    @foreach ($data['employees'] as $employee)
                                                                        <option value="{{$employee->id}}">{{$employee->name}} - {{$employee->roles->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>

                                                            {{-- <td class="form-group">
                                                                <textarea onchange="changeVal(this)" class="form-control required form-input" placeholder="Deskripsi Pekerjaan" rows="3" name="deskripsi[]"></textarea>
                                                            </td> --}}

                                                            <td class="text-center actionWork" style="width: 100px">
                                                                <a href='javascript:void(0)' onclick="addWork(this)" class='btn icon btn-sm btn-outline-primary rounded-pill'>
                                                                    <i class="fas fa-add"></i>
                                                                </a>
                                                                <a href='javascript:void(0)' onclick="removeWork(this)" class='btn icon btn-sm btn-outline-danger rounded-pill'>
                                                                    <i class="bi bi-trash-fill"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <script>
                                                            $.each(`{{$item['employe']}}`.split(","), function(i,e){
                                                                $(`.employee-{{$phaseNew}}-{{$key}} option[value='${e}']`).prop("selected", true);
                                                            });
                                                        </script>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <hr>
                        </div>

                        <div class="col-12 d-flex justify-content-end" id="button-container">
                            <button type="submit" id="btn-save" class="btn btn-primary me-1 mb-1">Submit</button>
                            <button type="button" class="btn btn-warning me-1 mb-1" onclick="cancel()" >Cancel</button>
                        </div>
                    </div>
                </form>
            </div>

            <div id="bobotContainer">
                <span>Total Bobot : <b id="bobotTotal">{{$data['totalBobot']}}</b></span>
            </div>
        </div>
    </section>
    @prepend('after-script')
    <script type="text/javascript" src="{{ asset('js/module/transaction/additionaltimeline/additionaltimeline.js') }}"></script>
</div>
