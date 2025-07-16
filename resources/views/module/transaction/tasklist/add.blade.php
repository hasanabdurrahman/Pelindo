<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Add Tasklist<i class="fas fa-refresh refresh-page"
                        onclick="renderView(`{!! route('tasklist.add') !!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Transaction</a></li>
                        <li class="breadcrumb-item spa_route" aria-current="page"><a href="javascript:void(0)"
                                onclick="renderView(`{!! route('transaction.tasklist') !!}`)">Tasklist</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section row">
        <div class="card">
            {{-- Empty div container to store hidden form input --}}

            <div id="user_res_data">

            </div>

            <div class="card-body">
                <form class="form form-vertical" id="add-tasklist" action="javascript:void(0)" method="POST"
                    enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-body">
                        <div class="row">

                            <input type="hidden" id="approve" class="form-input" name="approve">

                            <input type="hidden" id="karyawan_id" class="form-input form-control required"
                                name="karyawan_id" value='{{ auth()->user()->id }}' disabled>


                            <div class="col-md-6 col-12" style="display: none">
                                <div class="form-group">
                                    <label for="transactionnumber">Transaction Number</label>
                                    <input type="hidden" id="transactionnumber" class="form-input form-control"
                                        name="transactionnumber" disabled placeholder="Auto">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="project_id">Project</label>
                                    <select id="project_id" name="project_id" class="form-input form-control required"
                                        style="height:2rem">
                                        <option value="" hidden>-Pilih-</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->project_id }}">{{ $project->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="timelineA_id">Timeline</label>
                                    <select id="timelineA_id" name="timelineA_id"
                                        class="form-input form-control required" style="height:2rem">
                                        {{-- option input dari javascript --}}
                                    </select>
                                </div>

                                <div class="d-flex gap-2 align-items-center mb-2">
                                    <div class="form-check form-switch" style="padding-left: 0 !important">
                                        <span class="nav-link-text">Pekerjaan by Request Team</span>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input  me-0" type="checkbox" id="toggle-work">
                                        <label class="form-check-label"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="progress">Progress %</label>
                                    <input type="number" id="progress" class="form-input form-control required"
                                        name="progress" placeholder="ex: 1-100">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="tgl">Tanggal</label>
                                    <input type="date" class="form-control form-input required" name="tx_date" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-md-12 col-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea type="text" id="description" class="form-input form-control required" name="description" placeholder=""></textarea>
                                </div>
                            </div>
                            <div class="col-md-12 col-12">
                                <div class="form-group">
                                    <label for="image" class="form-label">Upload File</label>
                                    <input class="form-input form-control" type="file" id="image" name="image[]"
                                        multiple>
                                </div>
                            </div>
                            <div class="col-md-12 col-12" style="display:none" id="upload_document">
                                <div class="form-group">
                                    <label for="image" class="form-label">Upload Document</label>
                                    <input class="form-input form-control" type="file" id="document"
                                        name="document[]" multiple accept=".doc,.docx,.pdf">
                                </div>
                            </div>
                            <div class="mt-3 col-12 d-flex justify-content-end">
                                <button type="submit" id="btn-save"
                                    class="btn btn-primary me-1 mb-1">Submit</button>
                                <button type="button" class="btn btn-warning me-1 mb-1"
                                    onclick="renderView(`{!! route('transaction.tasklist') !!}`)">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@prepend('after-script')
    <script type="text/javascript" src="{{ asset('js/module/transaction/tasklist/tasklist.js') }}"></script>
