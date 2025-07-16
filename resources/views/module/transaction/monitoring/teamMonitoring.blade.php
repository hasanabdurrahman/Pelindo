<!-- HTML -->
<div class="card my-5">
    <div class="card-header">
        <h1>Project Deliveries</h1>
    </div>
    <div class="card-body mt-3">
      <div class="col-12 col-md-3">
          <h4>Progress</h4>
      </div>
      <div class="mt-5"><h5 id="progress-label">0%</h5></div>
      <div class="progress">
          <div id="progress-bar" class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" style="width: 0%" 
          aria-valuenow="0"
           aria-valuemin="0" 
           aria-valuemax="100">
        </div>
      </div>
  </div>
  
    <div class="card-body mt-3">
        <div class="mb-2 mt-3">
        </div>
        <div id="chart">
            
        </div>        
    </div>
</div>
@if ($data['timeline'] != null)
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade active show" id="table_view" role="tabpanel" aria-labelledby="table_tab">
        <div class="table-responsive table-borderless table-card px-3 mt-4">
            <table class="table table-centered align-middle table-nowrap mb-0 data-table-all" id="table_all"   
            style="width: 100%">
                <thead class="text-muted table-light">
                    <tr>
                        <th class="text-center" width="20px">No</th>
                        <th class="text-center">NIK</th>
                        <th class="text-center" style="width: 100px">Role</th>
                        <th class="text-center" style="width: 100px">Starts</th>
                        <th class="text-center">Contract</th>
                        <th class="text-center">Fase</th>
                        <th class="text-center">Detail</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Karyawan</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
 @else
    <div class="d-flex align-items-center justify-content-center m-5">
        <h6>Tidak ada data timeline, harap tambahkan timeline baru</h6>
    </div>
@endif

