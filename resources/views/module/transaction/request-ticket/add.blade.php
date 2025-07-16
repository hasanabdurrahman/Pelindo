<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>New Request<i class="fas fa-refresh refresh-page" onclick="renderView(`{!!route('request-ticket.add')!!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Transaction</a></li>
                        <li class="breadcrumb-item spa_route" aria-current="page"><a href="javascript:void(0)" onclick="renderView(`{!!route('transaction.request-ticket')!!}`)">Request ticket</a></li>
                        <li class="breadcrumb-item active" aria-current="page">New Request</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section row">
        <div class="card">
            <div class="card-body">
                <form class="form form-vertical" id="add-request" action="javascript:void(0)">
                    @csrf
                    <div class="form-body">
                        <div class="row">
                            {{-- <input type="hidden" id="karyawan_id" class="form-input form-control required" name="karyawan_id" value='{{auth()->user()->id}}' disabled> --}}
                        
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="project_id">Project</label>
                                    <select id="project_id" name="project_id"
                                        class="form-input form-control form-select required" data-placeholder="Pilih Project">
                                        <option></option>
                                        @foreach ($project as $item)
                                            <option value="{{ $item->id }}">{{ $item->name}} - {{$item->code}} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="karyawan_id">Employee</label>
                                    <select id="karyawan_id" name="karyawan_id"
                                        class="form-input form-control form-select-multiple required" data-placeholder="Pilih Person" style="height: 100%">
                                        {{-- onchange="checkAvail(this)" --}}
                                        <option></option>
                                        @foreach ($employee as $item)
                                            <option value="{{ $item->id }}">{{ $item->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="startdate">Start Date</label>
                                    <input type="date" id="startdate" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" class="form-input form-control required"
                                        name="startdate">
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="enddate">End Date</label>
                                    <input type="date" id="enddate" value="{{ date('Y-m-d', strtotime(date('Y-m-d') . '+1 days')) }}" min="{{ date('Y-m-d', strtotime(date('Y-m-d') . '+1 days')) }}" class="form-input form-control required"
                                        name="enddate">
                                </div>
                            </div>      

                            <div class="col-12 col-md-12 mb-2" id="notif-availibility">
                            </div>

                            <div class="col-md-12 col-12">
                                <div class="form-group">
                                    <label for="issue">Issue</label>
                                    <textarea type="text" id="issue" class="form-input form-control required" name="issue" placeholder=""></textarea>
                                </div>
                            </div>
                            <div class="mt-3 col-12 d-flex justify-content-end">
                                <button type="submit" id="btn-save" class="btn btn-primary me-1 mb-1">Submit</button>
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