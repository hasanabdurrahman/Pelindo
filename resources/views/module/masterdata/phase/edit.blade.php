<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Phase <i class="fas fa-refresh refresh-page"
                        onclick="renderView(`{!! route('phase.edit', base64_encode($phase->id)) !!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Master Data</a></li>
                        <li class="breadcrumb-item spa_route" aria-current="page"><a href="javascript:void(0)"
                                onclick="renderView(`{!! route('master-project.phase') !!}`)">Phase</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section row">
        <div class="card">
            <div class="card-body">
                <form class="form form-vertical" id="edit-phase" action="javascript:void(0)">
                    @csrf
                    <input type="hidden" name="id" value="{{$phase->id}}" class="form-input">
                    @method('PUT')
                    <div class="form-body">
                        <h5>Form input timeline type</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="type">Timeline Type</label>
                                    <input type="text" id="timeline" class="form-input form-control required" name="type" placeholder="Timelie Type" value="{{$phase->name}}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-body mt-3">
                        <div class="row">
                            <div class="col-md-6 col-12 order-md-1 order-first">
                                <h5>Form input phase list</h5>
                            </div>
                            <div class="col-md-6 col-12 order-md-2 order-last">
                                <div class="float-start float-lg-end">
                                    <a href='javascript:void(0)' onclick="addPhase(this)" class='btn icon btn-sm btn-outline-primary rounded-pill'>
                                        <i class="fas fa-add"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <ul id="sortable" class="form-phase-container" style="list-style: none">
                            @foreach ($phase->detail as $detail)
                                <li class="form-phase">
                                    <input type="hidden" id="phaseA_id" class="form-input" name="phaseA_id[]" value="{{$detail->id}}">
                                    <div class="row align-items-center">
                                        <div class="col-md-1 col-12">
                                            <button class="btn btn-sm btn-secondary" style="cursor:move">
                                                <i class="bi bi-justify"></i>
                                            </button>
                                        </div>
                                        <div class="col-md-5 col-12">
                                            <div class="form-group">
                                                <label for="type">Phase Name</label>
                                                <input type="text" id="name" class="form-input form-control required" name="name[]" placeholder="Phase Name" value="{{$detail->name}}">
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-5 col-12">
                                            <div class="form-group">
                                                <label for="type">Phase Order</label>
                                                <input type="number" id="order" class="form-input form-control required" name="order[]" placeholder="Phase Order" value="{{$detail->order}}">
                                            </div>
                                        </div> --}}
                                        <div class="col-md-2 col-12">
                                            <a href='javascript:void(0)' onclick="removePhase(this)" class='btn icon btn-sm btn-outline-danger rounded-pill btn-remove-work'>
                                                <i class="bi bi-trash-fill"></i>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <small >*Drag & drop phase untuk menentukan urutan phase</small>
                    </div>

                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" id="btn-update" class="btn btn-primary me-1 mb-1">Submit</button>
                        <button type="button" class="btn btn-warning me-1 mb-1"
                            onclick="renderView(`{!! route('master-project.phase') !!}`)">Cancel</button>
                    </div>
                </form>
            </div>

        </div>
    </section>

    @prepend('after-script')
    <script src="{{asset('js/module/masterdata/phase/phase.js')}}"></script>
</div>



