<div class="container">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="all-tab" data-bs-toggle="tab" href="#all"
                role="tab" aria-controls="all" aria-selected="true">All Tasklist</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="timeline-tab" data-bs-toggle="tab" href="#timeline"
                role="tab" aria-controls="timeline" aria-selected="false" tabindex="-1">Group By Project</a>
        </li>
    </ul>
</div>

<div class="card mt-3">
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade active show" id="all" role="tabpanel" aria-labelledby="all">
            <div class="table-responsive table-card px-3">
                <table class="table table-centered align-middle table-nowrap mb-0 table-tasklist" id="table" style="width: 100%">
                    <thead class="text-muted table-light">
                        <tr>
                            {{-- <th></th> --}}
                            <th>No</th>
                            <th>Transaction Number</th>
                            <th>Project</th>
                            <th>Client</th>
                            <th>Task</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tasklist['all'] as $key => $item)
                            <tr>
                                <td>{{(int)$key+1}}</td>
                                <td>{{$item->transactionnumber}} </td>
                                <td>{{$item->project->name}}</td>
                                <td>{{$item->project->client->name}}</td>
                                <td>{{isset($item->timelineA) ? $item->timelineA->detail : '-'}}</td>
                                <td>{{isset($item->timelineA) ? $item->timelineA->startdate : '-'}}</td>
                                <td>{{isset($item->timelineA) ? $item->timelineA->enddate : '-'}}</td>
                                <td>{{$item->progress}}%</td>
                            </tr>   
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="timeline" role="tabpanel" aria-labelledby="timeline">
            <ul class="nav nav-tabs" id="tab_tasklist" role="tablist">
                @foreach ($tasklist['groupBy'] as $key => $val)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="{{$key}}-tab" data-bs-toggle="tab" href="#{{$key}}"
                            role="tab" aria-controls="{{$key}}" aria-selected="true">{{str_replace('_', ' ',$key)}}</a>
                    </li>

                @endforeach
            </ul>

            <div class="tab-content mt-3" id="tab_tasklistContent">
                @foreach ($tasklist['groupBy'] as $key => $item)
                    <div class="tab-pane fade show" id="{{$key}}" role="tabpanel" aria-labelledby="all">
                        <div class="table-responsive table-card px-3">
                            @if (count($item) == 0)
                                <small class="text-danger">*timeline untuk project ini belum dibuat</small>
                            @endif
                            <table class="table table-centered align-middle table-nowrap mb-0 table-tasklist" id="table" style="width: 100%">
                                <thead class="text-muted table-light">
                                    <tr>
                                        {{-- <th></th> --}}
                                        <th>No</th>
                                        <th>Transaction Number</th>
                                        <th>Project</th>
                                        <th>Client</th>
                                        <th>Start</th>
                                        <th>End</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($item as $keyTask => $taskItem)
                                        <tr>
                                            <td>{{(int)$keyTask+1}}</td>
                                            <td>{{$taskItem->transactionnumber}} </td>
                                            <td>{{$taskItem->project->name}}</td>
                                            <td>{{$taskItem->project->client->name}}</td>
                                            <td>{{isset($taskItem->timelineA) ? $taskItem->timelineA->startdate : '-'}}</td>
                                            <td>{{isset($taskItem->timelineA) ? $taskItem->timelineA->enddate : '-'}}</td>
                                            <td>{{$taskItem->progress}}%</td>
                                        </tr>   
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    $('.table-tasklist').DataTable()
</script>