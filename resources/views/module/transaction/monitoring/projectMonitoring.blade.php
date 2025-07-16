@if ($data['timeline'] != null)
    <div class="col-12 col-md-6 order-md-1 order-last">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="table_view-tab" data-bs-toggle="tab" href="#table_view"
                    role="tab" aria-controls="table_view" aria-selected="true">Table View</a>
            </li>
            {{-- <li class="nav-item" role="presentation">
                <a class="nav-link" id="gantt-tab" data-bs-toggle="tab" href="#gantt"
                    role="tab" aria-controls="gantt" aria-selected="false" tabindex="-1">Gantt Chart</a>
            </li> --}}
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="team-tab" data-bs-toggle="tab" href="#team"
                    role="tab" aria-controls="team" aria-selected="false" tabindex="-1">Team Monitoring</a>
            </li>
            @endif
        </ul>
    </div>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade active show" id="table_view" role="tabpanel" aria-labelledby="table_tab">
            <div class="table-responsive table-borderless table-card px-3 mt-4">
                <table class="table table-centered align-middle table-nowrap mb-0 data-table" id="table" style="width: 100%">
                    <thead class="text-muted table-light">
                        <tr>
                            <th class="text-center" width="20px">No</th>
                            <th class="text-center">NIK</th>
                            <th class="text-center" style="width: 100px">Role</th>
                            <th class="text-center" style="width: 100px">Starts</th>
                            <th class="text-center">Contract</th>
                            <th class="text-center">Fase</th>
                            <th class="text-center">Detail</th>
                            <th class="text-center">Karyawan</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        {{-- <div class="tab-pane fade" id="gantt" role="tabpanel" aria-labelledby="gantt">
            
            @include('module.transaction.monitoring.ganttChart')
        
        </div>  --}}
        <div class="tab-pane fade" id="team" role="tabpanel" aria-labelledby="team">
            
            @include('module.transaction.monitoring.teamMonitoring')
        
        </div> 
        </div>
    </div>
{{-- @else
    <div class="d-flex align-items-center justify-content-center m-5">
        <h6>Tidak ada data timeline, harap tambahkan timeline baru</h6>
    </div>
@endif --}}