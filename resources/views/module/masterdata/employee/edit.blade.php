<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Employee<i class="fas fa-refresh refresh-page" onclick="renderView(`{!!route('employee.edit', $employee->id)!!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Master</a></li>
                        <li class="breadcrumb-item spa_route" aria-current="page"><a href="javascript:void(0)" onclick="renderView(`{!!route('masterdata.employee')!!}`)">Employee</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section row">
        <div class="card">
            {{-- Empty div container to store hidden form input --}}
            <div id="user_res_data">
            </div>

            <div class="card-body">
                <form class="form form-vertical" id="edit-user" action="javascript:void(0)">
                    @csrf
                    <div class="form-body">
                        <input type="hidden" name="id" value="{{$employee->id}}" class="form-input">
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="name">Nama User</label>
                                    <input type="text" id="name" class="form-input form-control required" name="name" placeholder="" value="{{$employee->name}}">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="code">NIK</label>
                                    <input type="text" id="code" class="form-input form-control required" name="code" placeholder=""value="{{$employee->code}}">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="email">Email </label>
                                    <input type="email" id="email" class="form-input form-control required" name="email" placeholder="ex: test@gmail.com"value="{{$employee->email}}">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="divisi_id">Divisi</label>
                                    <select id="divisi_id" name="divisi_id"
                                    class="form-input form-control required" style="height:2rem">
                                    @foreach ($division as $item)
                                    <option value="{{ $item->id }}" {{ $employee->divisi_id == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="address">Adrress</label>
                                    <input type="text" id="address" class="form-input form-control" name="address" placeholder="" value="{{$employee->address}}">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="roles_id">Jabatan</label>
                                    <select id="roles_id" name="roles_id"
                                    class="form-input form-control required" style="height:2rem">
                                    @foreach ($roles as $item)
                                    <option value="{{ $item->id }}" {{ $employee->roles_id == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" id="phone" class="form-input form-control" name="phone" placeholder=""value="{{$employee->phone}}">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="active">Status</label>
                                    <select id="select2" id="active" name="active"
                                    class="form-input form-control required" style="height:2rem">
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                </select>
                                </div>
                            </div>
                            <input type="hidden" id="deleted_status" name="deleted_status" value="{{ $employee->deleted_status }}">
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" id="btn-update" class="btn btn-primary me-1 mb-1">Save</button>
                                <button type="button" class="btn btn-warning me-1 mb-1" onclick="renderView(`{!!route('masterdata.employee')!!}`)" >Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </section> 
    @prepend('after-script')
    <script type="text/javascript" src="{{ asset('js/module/masterdata/employee/employee.js') }}">
    $(document).ready(function () {
    $('#active').change(function () {
        var selectedValue = $(this).val();
        var deletedStatus = (selectedValue === 'Active') ? 0 : 1;
        $('#deleted_status').val(deletedStatus);
    });
});
</script>
</div>