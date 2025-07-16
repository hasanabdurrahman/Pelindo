
@foreach ($data['phase'] as $phase => $items)
    <div class="my-5">
        <div class="d-flex flex-row-reverse mb-3">
            <button class="btn icon icon-left btn-outline-secondary btn-sm rounded-pill" onclick="addPhase('{{$data['project']->id}}')">
                <i class="fas fa-add"></i>
                Add Phase
            </button>
        </div>

        <div class="form-group row align-items-center">
            <div class="col-md-1 col-12 deletePhase">
                <a href='javascript:void(0)' onclick="removePhase(this)" class='btn icon btn-sm btn-outline-danger rounded-pill'>
                    <i class="bi bi-trash-fill"></i>
                </a>
            </div>
            <div class="col-md-2 col-12">
                <label class="col-form-label">Phase Name</label>
            </div>
            <div class="col-md-9 col-12">
                <input type="text" id="phase" class="form-control required phase" name="show_phase[]" value="{{$phase}}" placeholder="Phase Name" onchange="changeVal(this)" onkeydown="changeVal(this)" >
            </div>
        </div>

        <div class="col-12 col-md-12 mt-2">
            <div class="table-responsive table-card px-3">
                <table class="table table-centered align-middle table-borderless table-nowrap mb-0 data-table" id="example" style="width: 100%">
                    <thead class="text-muted table-light">
                        <tr>

                            <th class="text-center" style="width: 200px">Pekerjaan</th>
                            <th class="text-center" style="width: 50px">Start Date</th>
                            <th class="text-center" style="width: 50px">End Date</th>
                            <th class="text-center" style="width: 100px">Bobot</th>
                            <th class="text-center" style="width: 200px">Person</th>
                            <th class="text-center" style="width: 200px">Need Document</th>
                            <th scope="text-center" style="width: 150px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $key => $item)
                            <tr>
                                <td class="form-group">
                                    <input type="hidden" id="phase-hidden" class="form-input required" name="phase[]" value="{{$phase}}">
                                    <input onchange="changeVal(this)" onkeydown="changeVal(this)" type="text" id="work" class="form-input form-control required" name="work[]" placeholder="Work Name" value="{{$item['task']}}">
                                </td>

                                <td class="form-group">
                                    <input onchange="changeVal(this)" onkeydown="return false" min="{{$data['project']->startdate}}" max="{{$data['project']->enddate}}" type="date" id="start_date" class="form-input form-control required" name="start_date[]" placeholder="Start Date" value="{{$item['startdate']}}">
                                </td>

                                <td class="form-group">
                                    <input onchange="changeVal(this)" onkeydown="return false" min="{{$data['project']->startdate}}" max="{{$data['project']->enddate}}" type="date" id="end_date" class="form-input form-control required" name="end_date[]" placeholder="End Date" value="{{$item['enddate']}}">
                                </td>

                                <td class="form-group">
                                    <input onchange="changeVal(this)" onkeydown="changeVal(this)" onblur="countBobot(this)" type="text" id="bobot" max="10" class="form-input form-control required" name="bobot[]" placeholder="Bobot" value="{{$item['bobot']}}">
                                </td>

                                <td class="form-group text-center">
                                    <div id="default-emp">
                                        <div class="row align-items-center">
                                            <input type="hidden" name="default_karyawan_id[]" class="form-input" value="{{$item['employe']}}">
                                            <div class="col-12 col-md-12">
                                                {!!$item['employe_html']!!}
                                            </div>
                                            <div class="col-12 col-md-12 mt-2">
                                                <a href='javascript:void(0)' onclick="changePerson(this)" class='btn icon btn-sm btn-outline-warning rounded-pill'>
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="form-emp" style="display: none">
                                        <div class="row align-items-center">
                                            <div class="col-12 col-md-11">
                                                <select onchange="changeVal(this)" class="form-select select-employee form-input" multiple="multiple" name="employee[]" style="width: 100%">
                                                    @foreach ($data['employees'] as $employee)
                                                        <option value="{{$employee->id}}">{{$employee->name}} - {{$employee->roles->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12 col-md-12 mt-2">
                                                <a href='javascript:void(0)' onclick="changePerson(this)" class='btn icon btn-sm btn-outline-danger rounded-pill'>
                                                    <i class="fas fa-close"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="form-group text-center" style="margin-left: auto;margin-right:auto">
                                    <input class="form-check-input me-0" type="checkbox" id="toggle-work" name="document_check[]" onchange="changeVal(this)" @if ($item['is_document'] == 1)
                                        @checked(true)
                                    @endif>
                                    <input type="hidden" name="is_document[]" class="form-input" value="{{$item['is_document']}}">
                                </td>

                                <td class="text-center actionWork" style="width: 100px">
                                    <a href='javascript:void(0)' onclick="addWork(this)" class='btn icon btn-sm btn-outline-primary rounded-pill'>
                                        <i class="fas fa-add"></i>
                                    </a>
                                    <a href='javascript:void(0)' onclick="removeWork(this)" class='btn icon btn-sm btn-outline-danger rounded-pill btn-remove-work'>
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <hr>
@endforeach
<script>
    $('.select-employee').select2();
    $('input[name="bobot[]"]').inputmask({ regex: "^[1-9][0-9]?$|^100$", placeholder: '' })
</script>
