<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Additional Timeline <i class="fas fa-refresh refresh-page" onclick="renderView(`{!!route('additional-timeline.show', base64_encode($data['timeline']->id))!!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Transaction</a></li>
                        <li class="breadcrumb-item spa_route" aria-current="page"><a href="javascript:void(0)" onclick="renderView(`{!!route('master-project.additional-timeline')!!}`)">Additional Timeline</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Show</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section row">
        <div class="card">
            <div class="card-body">
                {{-- HEADER --}}
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="tn_number">Transaction Number</label>
                            <input type="text" name="default_transactionnumber" id="tn_number" class="form-input form-control" value="{{$data['timeline']->transactionnumber}}" disabled>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label for="project_show">Project Name</label>
                            <input type="text" name="project_show" id="project_show" class="form-input form-control" value="{{$data['project']->name}} ({{$data['project']->code}})" disabled>
                        </div>
                    </div>
                </div>

                {{-- DETAIL --}}
                <div id="phase-container">
                    @foreach ($data['phase'] as $phase => $items)
                    <div class="my-5">
                        <div class="form-group row align-items-center">
                            <div class="col-md-2 col-12">
                                <label class="col-form-label">Phase Name</label>
                            </div>
                            <div class="col-md-9 col-12">
                                <input type="text" id="phase" class="form-control required phase" disabled name="show_phase[]" value="{{$phase}}" placeholder="Phase Name" onchange="changeVal(this)" onkeydown="changeVal(this)" >
                            </div>
                        </div>

                        <div class="col-12 col-md-12 mt-2">
                            <div class="table-responsive table-card px-3">
                                <table class="table table-striped table-centered align-middle table-borderless table-nowrap mb-0 data-table" id="example" style="width: 100%">
                                    <thead class="text-muted table-light">
                                        <tr>

                                            <th class="text-center">Pekerjaan</th>
                                            <th class="text-center">Start Date</th>
                                            <th class="text-center">End Date</th>
                                            <th class="text-center">Bobot</th>
                                            <th class="text-center">Person</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $key => $item)
                                            <tr>
                                                <td>
                                                    {{$item['task']}}
                                                </td>
                                                <td class="text-center">
                                                    {{$item['startdate']}}
                                                </td>
                                                <td class="text-center">
                                                    {{$item['enddate']}}
                                                </td>
                                                <td class="text-center">
                                                    {{$item['bobot']}}
                                                </td>
                                                <td>
                                                    <div class="row">
                                                        <input type="hidden" name="default_karyawan_id[]" class="form-input" value="{{$item['employe']}}">
                                                        <div class="col-12 col-md-12">
                                                            {!!$item['employe_html']!!}
                                                        </div>
                                                    </div>
                                                </td>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <hr>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @prepend('after-script')
    <script type="text/javascript" src="{{ asset('js/module/transaction/additionaltimeline/additionaltimeline.js') }}"></script>
</div>
