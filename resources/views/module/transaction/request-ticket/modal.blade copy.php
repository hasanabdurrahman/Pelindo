<div class="modal fade" id="modal-reject" tabindex="-1" aria-labelledby="modal-reject" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5">Reject Approved Ticket</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="javascript:void(0)" id="reason-reject">
              @csrf
              <input type="hidden" name="ticket_id" class="form-input">
                <div class="form-group">
                    <label for="notes">Alasan Reject</label>
                    <input type="text" name="reason" id="reason" class="form-control form-input required">
                </div>
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary btn-confirm" onclick="reject()">Confirm</button>
        </div>
      </div>
    </div>
</div>

<div class="modal fade" id="modal-history" tabindex="-1" aria-labelledby="modal-reject" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5">History Approval Ticket</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>
