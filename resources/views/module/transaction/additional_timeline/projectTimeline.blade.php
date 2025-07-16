@if ($data['timeline'] != null && $data['mainTimeline'] != null)
    <div class="col-12 col-md-6 order-md-1 order-last">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="logChange-tab" data-bs-toggle="tab" href="#logChange"
                    role="tab" aria-controls="logChange" aria-selected="true">Log Additional Timeline</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="currentTimeline-tab" data-bs-toggle="tab" href="#currentTimeline"
                    role="tab" aria-controls="currentTimeline" aria-selected="false" tabindex="-1"
                    onclick="getCurrentTimeline()">
                    Current Timeline
                </a>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade active show" id="logChange" role="tabpanel" aria-labelledby="logChange">
            <div class="table-responsive table-borderless table-card px-3 mt-4">
                <table class="table table-centered table-striped align-middle mb-0 data-table" id="example" style="min-width: 100%">
                    <thead class="text-muted table-light">
                        <tr>
                            <th>No</th>
                            <th>Status</th>
                            <th>Additional Number</th>
                            <th>Transaction Number</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="currentTimeline" role="tabpanel" aria-labelledby="currentTimeline">
            @include('module.transaction.additional_timeline.currentTimeline')
        </div>
    </div>
@else
    <div class="d-flex align-items-center justify-content-center m-5">
        <h6 class="text-danger text-center">Tidak ada data timeline utama <b>ATAU</b> data timeline utama <b> belum di approve </b>, harap hubungi PC / KADEP terkait</h6>
    </div>
@endif