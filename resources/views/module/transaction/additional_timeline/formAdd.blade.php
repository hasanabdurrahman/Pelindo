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
            <input type="text" id="phase" class="form-control required phase" name="show_phase[]" placeholder="Phase Name" onchange="changeVal(this)" onkeydown="changeVal(this)" >
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
                        {{-- <th class="text-center" style="width: 200px">Deskripsi</th> --}}
                        <th scope="text-center" style="width: 150px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="form-group">
                            <input type="hidden" class="form-input timelineA_id" name="timelineA_id[]">
                            <input type="hidden" id="phase-hidden" class="form-input required" name="phase[]">
                            <input onchange="changeVal(this)" onkeydown="changeVal(this)" type="text" id="work" class="form-input form-control required" name="work[]" placeholder="Work Name">
                        </td>

                        <td class="form-group">
                            <input onchange="changeVal(this)" onkeydown="return false" min="{{$data['project']->startdate}}" type="date" id="start_date" class="form-input form-control required" name="start_date[]" placeholder="Start Date" value="{{date('Y-m-d')}}">
                        </td>

                        <td class="form-group">
                            <input onchange="changeVal(this)" onkeydown="return false" max="{{$data['project']->enddate}}" type="date" id="end_date" class="form-input form-control required" name="end_date[]" placeholder="End Date" value="{{date('Y-m-d')}}">
                        </td>

                        <td class="form-group">
                            <input onchange="changeVal(this)" onkeydown="changeVal(this)" onblur="countBobot(this)" type="text" id="bobot" max="10" class="form-input form-control required" name="bobot[]" placeholder="Bobot">
                        </td>

                        <td class="form-group">
                            <select onchange="changeVal(this)" class="form-select select-employee form-input required" multiple="multiple" name="employee[]" style="width: 100%">
                                @foreach ($data['employees'] as $employee)
                                    <option value="{{$employee->id}}">{{$employee->name}} - {{$employee->roles->name}}</option>
                                @endforeach
                            </select>
                            {{-- onchange="changeVal(this)" <input type="text" id="tim" class="form-input form-control required" name="tim[]" placeholder="Tim"> --}}
                        </td>

                        {{-- <td class="form-group">
                            <textarea onchange="changeVal(this)" class="form-control required form-input" placeholder="Deskripsi Pekerjaan" rows="3" name="deskripsi[]"></textarea>
                        </td> --}}

                        <td class="text-center actionWork" style="width: 100px">
                            <a href='javascript:void(0)' onclick="addWork(this)" class='btn icon btn-sm btn-outline-primary rounded-pill'>
                                <i class="fas fa-add"></i>
                            </a>
                            <a href='javascript:void(0)' onclick="removeWork(this)" class='btn icon btn-sm btn-outline-danger rounded-pill'>
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
    $('.select-employee').select2();
    $('input[name="bobot[]"]').inputmask({ regex: "^[1-9][0-9]?$|^100$", placeholder: '' })
</script>
<hr>