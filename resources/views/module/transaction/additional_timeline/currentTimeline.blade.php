<div class="card">
    <div class="card-body">
        <div class="col-12 col-md-6 order-md-1 order-last">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="tableView-tab" data-bs-toggle="tab" href="#tableView"
                        role="tab" aria-controls="tableView" aria-selected="true">Table View</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="gantt-tab" data-bs-toggle="tab" href="#gantt"
                        role="tab" aria-controls="gantt" aria-selected="false" tabindex="-1">Gantt Chart</a>
                </li>
            </ul>
        </div>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade active show" id="tableView" role="tabpanel" aria-labelledby="tableView">
                <div class="table-responsive table-borderless table-card px-3 mt-4">
                    <table class="table table-centered table-striped align-middle mb-0 data-table-current" id="example" style="min-width: 100%">
                        <thead class="text-muted table-light">
                            <tr>
                                <th>No</th>
                                <th>Status</th>
                                <th>Transaction Number</th>
                                <th>Task</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Bobot</th>
                                <th>Karyawan</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="gantt" role="tabpanel" aria-labelledby="gantt">
                @include('module.transaction.additional_timeline.ganttChart')
            </div>
        </div>
    </div>
</div>