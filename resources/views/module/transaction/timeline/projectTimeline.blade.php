@if ($data['timeline'] != null)
        <span>Status : {!! $data['timeline']->approved_by != null ? '<span class="badge bg-light-success">Approved</span>' : '<span class="badge bg-light-danger">Not Approved</span>'!!} </span>
    <hr>

    <div class="col-12 col-md-6 order-md-1 order-last">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="table_view-tab" data-bs-toggle="tab" href="#table_view"
                    role="tab" aria-controls="table_view" aria-selected="true">Table View</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="gantt-tab" data-bs-toggle="tab" href="#gantt"
                    role="tab" aria-controls="gantt" aria-selected="false" tabindex="-1">Gantt Chart</a>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade active show" id="table_view" role="tabpanel" aria-labelledby="table_tab">
            <div class="table-responsive table-borderless table-card px-3 mt-4">
                <table class="table table-centered table-striped align-middle mb-0 data-table" id="example">
                    <thead class="text-muted table-light">
                        <tr>
                            <th class="text-center" width="20px">No</th>
                            <th class="text-center"></th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Transaction Number</th>
                            <th class="text-center" style="width: 100px">Task</th>
                            <th class="text-center" style="width: 100px">Start Date</th>
                            <th class="text-center" style="width: 100px">End Date</th>
                            <th class="text-center">Bobot</th>
                            <th class="text-center">Karyawan</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="gantt" role="tabpanel" aria-labelledby="gantt">
            {{-- @for ($i = 0; $i < $count; $i++)
                
            @endfor --}}
            @include('module.transaction.timeline.ganttChart')
            {{-- <div id="chart"></div> --}}
        </div>
    </div>
@else
    <div class="d-flex align-items-center justify-content-center m-5">
        <h6>Tidak ada data timeline, harap tambahkan timeline baru</h6>
    </div>
@endif