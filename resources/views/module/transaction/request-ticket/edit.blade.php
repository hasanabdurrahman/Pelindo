<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Request Ticket<i class="fas fa-refresh refresh-page" onclick="renderView(`{!!route('request-ticket.edit', base64_encode($storedData->id))!!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Transaction</a></li>
                        <li class="breadcrumb-item spa_route" aria-current="page"><a href="javascript:void(0)" onclick="renderView(`{!!route('transaction.request-ticket')!!}`)">Request</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section row">
        <div class="card">
            <div class="card-body">
                <form class="form form-vertical" id="edit-request" action="javascript:void(0)">
                    @csrf
                    <div class="form-body">
                        <input type="hidden" name="id" value="{{$storedData->id}}" class="form-input">
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="project_id">Project</label>
                                    <select id="project_id" name="project_id"
                                    class="form-input form-control form-select required">
                                    @foreach ($project as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $storedData->project_id == $item->id ? 'selected' : '' }}>
                                            {{$item->name}}
                                        </option>
                                    @endforeach
                                
                                </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="karyawan_id">Employee</label>
                                    <select id="karyawan_id" name="karyawan_id" class="form-input form-control form-select required" onchange="checkAvail(this)"> 
                                        @foreach ($employee as $item)
                                        <option value='{{ $item->id}}'
                                            {{ $storedData->karyawan_id == $item->id ? 'selected' : '' }}>
                                            {{$item->name}}
                                        </option>
                                        @endforeach
                                    </select>   
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="startdate">Start Date</label>
                                    <input type="date" id="startdate" value="{!! \Carbon\Carbon::parse($storedData->startdate)->translatedFormat('Y-m-d')!!}" class="form-input form-control required" name="startdate">
                                </div>
                            </div>
    
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="enddate">End Date</label>
                                    <input type="date" id="enddate"  value="{!! \Carbon\Carbon::parse($storedData->enddate)->translatedFormat('Y-m-d')!!}" class="form-input form-control required" name="enddate">
                                </div>
                            </div>

                            <div class="col-md-12 col-12">
                                <div class="form-group">
                                    <label for="issue">Issue</label>
                                    <textarea type="text" id="issue" class="form-input form-control required" name="issue" placeholder="" value="{{$storedData->issue}}">{{$storedData->issue}}</textarea>
                                </div>
                            </div>

                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" id="btn-update" class="btn btn-primary me-1 mb-1">Save</button>
                                <button type="button" class="btn btn-warning me-1 mb-1" onclick="renderView(`{!!route('transaction.request-ticket')!!}`)" >Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section> 
</div>
@prepend('after-script')
<script type="text/javascript" src="{{ asset('js/module/transaction/request-ticket/request-ticket.js') }}"></script>