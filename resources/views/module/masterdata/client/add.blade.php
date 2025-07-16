<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tambah Client <i class="fas fa-refresh refresh-page"
                        onclick="renderView(`{!! route('client.add') !!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Master</a></li>
                        <li class="breadcrumb-item spa_route" aria-current="page"><a href="javascript:void(0)"
                                onclick="renderView(`{!! route('master-project.client') !!}`)">Client</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section row">
        <div class="card">
            {{-- Empty div container to store hidden form input --}}
            <div id="roles_res_data">

            </div>

            <div class="card-body">
                <form class="form form-vertical" id="add-client" action="javascript:void(0)">
                    @csrf
                    <div class="form-body">
                        <div class="row">

                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="code">Code Client</label>
                                    <input type="text" id="clientCode" class="form-input form-control required"
                                        name="code" placeholder="Client Code (Max 3 Character)">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="name">Nama Perusahaan</label>
                                    <input type="text" id="companyName" class="form-input form-control required"
                                        name="name" placeholder="Company Name">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="contact_person">Contact Person</label>
                                    <input type="text" id="contactPerson" class="form-input form-control required"
                                        name="contact_person" placeholder="Contact Person">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="company_phone">Company Phone</label>
                                    <input type="text" id="companyPhone" class="form-input form-control required"
                                        name="company_phone" placeholder="Company Phone">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="clientEmail" class="form-input form-control required"
                                        name="email" placeholder="Email">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="company_address">Company Address</label>
                                    <input type="text" id="companyAddress" class="form-input form-control required"
                                        name="company_address" placeholder="Company Address">
                                </div>
                            </div>



                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" id="btn-save" class="btn btn-primary me-1 mb-1">Submit</button>
                                <button type="button" class="btn btn-warning me-1 mb-1"
                                    onclick="renderView(`{!! route('master-project.client') !!}`)">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </section>
</div>
@prepend('after-script')
   <script type="text/javascript" src="{{ asset('js/module/masterdata/client/client.js') }}"></script>
