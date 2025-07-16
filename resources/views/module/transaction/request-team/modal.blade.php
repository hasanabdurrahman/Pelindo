<!-- Modal Review -->
<div class="modal fade" id="modal-review" tabindex="-1" aria-labelledby="modal-review" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="modal-review">Review Status Pekerjaan</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <h6>Summary Pekerjaan</h6>
            <hr>
                <div id="summary-container">
                    
                </div>
            <hr>
            <h6>List Detail Pekerjaan</h6>
            <hr>
                <div id="list-tasklist">

                </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" id="btn-reject">Reject</button>
          <button type="button" class="btn btn-primary" id="btn-approve">Approve</button>
        </div>
      </div>
    </div>
</div>

<!-- Modal Reason Rejected -->
<div class="modal fade" id="modal-rejected" tabindex="-1" aria-labelledby="modal-rejected" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="modal-review">Request Rejected Reason</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-12 col-md-3">
                    <h6>Project</h6>
                </div>
                <div class="col-12 col-md-2">:</div>
                <div class="col-12 col-md-7" id="project"></div>
            </div>
            <div class="row">
                <div class="col-12 col-md-3">
                    <h6>Start Date</h6>
                </div>
                <div class="col-12 col-md-2">:</div>
                <div class="col-12 col-md-7" id="startDate"></div>
            </div>
            <div class="row">
                <div class="col-12 col-md-3">
                    <h6>End Date</h6>
                </div>
                <div class="col-12 col-md-2">:</div>
                <div class="col-12 col-md-7" id="endDate"></div>
            </div>
            <div class="row">
                <div class="col-12 col-md-3">
                    <h6>Reason</h6>
                </div>
                <div class="col-12 col-md-2">:</div>
                <div class="col-12 col-md-7" id="reason"></div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Reject</button>
        </div>
      </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="modal-reject" tabindex="-1" aria-labelledby="modal-reject" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="modal-reject">Reject Request Team</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="javascript:void(0)" id="reason-reject">
              @csrf
                <div class="form-group">
                    <label for="reason_reject">Alasan Reject</label>
                    <input type="text" name="reason" id="reason" class="form-control form-input required">
                </div>
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary btn-confirm">Confirm</button>
        </div>
      </div>
    </div>
</div>