<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Phase <i class="fas fa-refresh refresh-page"
                        onclick="renderView(`{!! route('phase.show', base64_encode($phase->id)) !!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Master Data</a></li>
                        <li class="breadcrumb-item spa_route" aria-current="page"><a href="javascript:void(0)"
                                onclick="renderView(`{!! route('master-project.phase') !!}`)">Phase</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section row">
        <div class="card">
            <div class="card-body">
                <div class="form-body">
                    <h5>Form input timeline type</h5>
                    <hr>
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="type">Timeline Type</label>
                                <input disabled type="text" id="timeline" class="form-input form-control required" name="type" placeholder="Timelie Type" value="{{$phase->name}}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-body mt-3">
                    <div class="row">
                        <div class="col-md-6 col-12 order-md-1 order-first">
                            <h5>Form input phase list</h5>
                        </div>
                    </div>
                    <hr>
                    <div id="form-phase-container">
                        @foreach ($phase->detail as $detail)
                            <div class="row align-items-center form-phase">
                                <div class="col-md-5 col-12">
                                    <div class="form-group">
                                        <label for="type">Phase Name</label>
                                        <input disabled type="text" id="name" class="form-input form-control required" name="name[]" placeholder="Phase Name" value="{{$detail->name}}">
                                    </div>
                                </div>
                                <div class="col-md-5 col-12">
                                    <div class="form-group">
                                        <label for="type">Phase Order</label>
                                        <input disabled type="number" id="order" class="form-input form-control required" name="order[]" placeholder="Phase Order" value="{{$detail->order}}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" id="btn-update" class="btn btn-primary me-1 mb-1">Submit</button>
                    <button type="button" class="btn btn-warning me-1 mb-1"
                        onclick="renderView(`{!! route('master-project.phase') !!}`)">Cancel</button>
                </div>
            </div>
        </div>
    </section>

    @prepend('after-script')
    <script src="{{asset('js/module/masterdata/phase/phase.js')}}"></script>
</div>



