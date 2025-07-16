<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Timeline Report <i class="fas fa-refresh refresh-page" onclick="renderView(`{!!route('report.timeline-report')!!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Report</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Timeline</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <section class="section row">
        <div class="card">
            <div class="card-body">
                <form class="form form-vertical" id="timeline-report" action="javascript:void(0)">
                    @csrf
                    <div class="row align-items-center justify-content-between">
                        <div class="col-12">
                            <h5>Periode Timeline</h5>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">Start Date</label>
                                <input type="date" name="startdate" id="startdate" class="form-control form-input required" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">End Date</label>
                                <input type="date" name="enddate" id="enddate" class="form-control form-input required" value="{{ date('Y-m-d', strtotime(date('Y-m-d') . '+1 days')) }}" min="{{ date('Y-m-d', strtotime(date('Y-m-d') . '+1 days')) }}">
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-center justify-content-between mt-2">
                        <div class="col-12">
                            <h5>Status & Filter</h5>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">Status</label>
                                <select name="status" id="status" class="form-control form-select form-input">
                                    <option value="all">All</option>
                                    <option value="finish">Finish</option>
                                    <option value="late">Late</option>
                                    <option value="on_progress">On Progress</option>
                                    <option value="up_comming">Up Comming</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label for="">Filter By</label>
                                <select name="filter" id="filter" class="form-control form-select form-input">
                                    <option value="" selected>None</option>
                                    <option value="project">Project</option>
                                    <option value="pc">PC</option>
                                    <option value="karyawan">Karyawan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-12" id="search-form" style="display: none">
                            <div class="form-group">
                                <label for="search">Filter Search</label>
                                <input type="text" name="search" id="" class="form-control form-input">
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 col-12 d-flex justify-content-end">
                        <button type="submit" id="btn-preview" class="btn btn-primary me-1 mb-1">Show Preview</button>
                        <div class="btn-group dropup me-1 mb-1">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Print Report
                            </button>
                            <div class="dropdown-menu" style="">
                                <a class="dropdown-item btn-print" data-mode="pdf" href="javascript:void(0)">PDF</a>
                                <a class="dropdown-item btn-print" data-mode="excel" href="javascript:void(0)">Excel</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </section> 

    {{-- Preview --}}
    <section class="section row" id="preview" style="display: none">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    {{-- Left Nav --}}
                    <div class="col-12 col-md-6 order-md-1 order-last">
                    </div>

                    {{-- Right Nav --}}
                    <div class="col-12 col-md-6 order-md-2 order-first ">
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive table-card px-3">
                    <table class="table table-centered align-middle table-nowrap mb-0 data-table" id="example" style="width: 100%">
                        <thead class="text-muted table-light" id="thead"></thead>
                        <tbody id="tbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section> 

    {{-- @include('module.setting.menu.modal') --}}
    @prepend('after-script')
    <script type="text/javascript" src="{{ asset('js/module/report/timeline-report/timeline-report.js') }}"></script>
</div>