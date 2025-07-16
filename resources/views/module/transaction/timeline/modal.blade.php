<!-- Modal Reject -->
<div class="modal fade" id="modal-template" tabindex="-1" aria-labelledby="modal-reject" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="modal-template">Insert Timeline dengan Template</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body py-5">
            <div id="choose-timeline-type">
                <form action="javascript:void(0)" id="form-generate" class="form form-vertical">
                    <center>
                        <h6>Please Choose Timeline Type</h6>
                        <div class="form-group" style="width:300px">
                            <input type="hidden" name="project_id" class="form-input">
                            <select class="form-select form-input required" name="timeline_type" id="timeline-type">
                                <option value="" selected disabled>--- Choose Timeline Type ---</option>
                                @foreach ($timeline_type as $ph)
                                    <option value="{{$ph->id}}">{{$ph->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="my-3">
                            <button class="btn btn-sm btn-primary" type="submit">
                                Generate Template
                            </button>
                        </div>

                        <small class="mt-2">Template sudah ada? <a href="javascript:void(0)" class="text-primary" onclick="byPassGenerateTemplate()">langsung import timeline</a></small>
                    </center>
                </form>
            </div>
            <div id="form-import-container" style="display:none">
                <form action="javascript:void(0)" id="form-import" enctype="multipart/form-data">
                  @csrf
                    <div class="form-group">
                        <label>Timeline Project</label>
                        <input type="file" name="file" class="form-control form-input required">
                    </div>

                    <div class="col-12 d-flex justify-content-end" id="button-container">
                        <button type="submit" class="btn btn-sm btn-primary me-1 mb-1">Submit</button>
                        <button type="button" class="btn btn-warning btn-sm me-1 mb-1" onclick="cancelImport()" >Cancel</button>
                    </div>
                </form>
            </div>
        </div>
      </div>
    </div>
</div>
