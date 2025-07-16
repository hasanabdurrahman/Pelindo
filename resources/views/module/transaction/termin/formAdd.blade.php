<div class="my-5">
    <div class="col-12 col-md-12 mt-2">
        <div class="table-responsive table-card px-3">
            <table class="table table-centered align-middle table-borderless table-nowrap mb-0" id="example" style="width: 100%">
                <thead class="text-muted table-light">
                    <tr>
                        <th class="text-center" style="width: 100px">Nama Termin</th>
                        <th class="text-center" style="width: 50px">Percentase</th>
                        <th class="text-center" style="width: 200px">Value</th>
                        <th class="text-center" style="width: 150px">Pekerjaan</th>
                        <th class="text-center" style="width: 50px">Due Date</th>
                        <th scope="text-center" style="width: 100px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        {{-- <td class="form-group">
                            <input onchange="changeVal(this)" onkeydown="return false" min="{{$data['project']->startdate}}" max="{{$data['project']->enddate}}" type="date" id="start_date" class="form-input form-control required" name="start_date[]" placeholder="Start Date" value="{{date('Y-m-d')}}">
                        </td> --}}

                        <td class="form-group">
                            <input onchange="changeVal(this)" onkeydown="changeVal(this)" type="text" id="name" class="form-input form-control required" name="name[]" placeholder="Termin Name">
                        </td>

                        <td class="form-group">
                            <input onchange="changeVal(this)" onkeydown="changeVal(this)" onblur="countValue(this)" type="text" id="percentage" max="10" class="form-input form-control required" name="percentage[]" placeholder="Persentase (%)">
                        </td>

                        <td class="form-group">
                            <input type="text" id="value" class="form-input form-control required" name="value[]" placeholder="Termin Value" readonly>
                        </td>

                        <td class="form-group">
                            <select onchange="changeVal(this)" class="form-select select-timelineA form-input required" name="timelineA_id[]" style="width: 100%" data-placeholder="Pilih Pekerjaan">
                                <option></option>
                                @foreach ($timelineA as $work)
                                    <option value="{{$work->id}}" data-duedate="{{$work->enddate}}">{{$work->detail}}</option>
                                @endforeach
                            </select>
                        </td>

                        <td class="form-group">
                            <input onkeydown="return false" readonly type="date" id="due_date" class="form-input form-control required" name="due_date[]" placeholder="Due Date">
                        </td>

                        <td class="text-center actionTermin" style="width: 100px">
                            <a href='javascript:void(0)' onclick="addTermin(this)" class='btn icon btn-sm btn-outline-primary rounded-pill'>
                                <i class="fas fa-add"></i>
                            </a>
                            <a href='javascript:void(0)' onclick="removeTermin(this)" class='btn icon btn-sm btn-outline-danger rounded-pill btn-remove-termin' style="display: none">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $('input[name="percentage[]"]').inputmask({ regex: "^[1-9][0-9]?$|^100$", placeholder: '' })
</script>
<hr>
