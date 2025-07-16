<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Project <i class="fas fa-refresh refresh-page"
                        onclick="renderView(`{!! route('project.edit', $project->id) !!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Master</a></li>
                        <li class="breadcrumb-item spa_route" aria-current="page"><a href="javascript:void(0)"
                                onclick="renderView(`{!! route('master-project.project') !!}`)">Project</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section row">
        <div class="card">
            {{-- Empty div container to store hidden form input --}}
            <div id="project_res_data">

            </div>

            <div class="card-body">
                <form class="form form-vertical" id="edit-project" action="javascript:void(0)">
                    @csrf
                    <input type="hidden" name="id" value="{{ $project->id }}" class="form-input">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="code">Code</label>
                                    <input type="text" id="code"  value="{{ $project->code }}" class="form-input form-control required"
                                        name="code" disabled>
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" id="name" value="{{ $project->name }}"
                                        class="form-input form-control required" name="name" required>
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="id_client">Client ID</label>
                                    <select id="id_client" name="id_client" class="form-input form-control required"
                                        style="height: 2rem">
                                        @foreach ($client as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $project->id_client == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="contract_number">Contract Number</label>
                                    <input type="text" id="contract_number"
                                        value="{{ $project->contract_number }}"class="form-input form-control required"
                                        name="contract_number">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="startdate">Start Date</label>
                                    <input type="date" id="startdate" value="{{ $project->startdate }}"
                                        class="form-input form-control required" name="startdate" required>
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="enddate">End Date</label>
                                    <input type="date" id="enddate"
                                        value="{{ $project->enddate }}"class="form-input form-control required"
                                        name="enddate" required>
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="value">Value</label>
                                    <input type="text" id="value" value="{{ number_format($project->value) }}" class="form-input form-control required" name="value" required>
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="pc_id">Project Coordinator</label>
                                    <select id="pc_id" name="pc_id" class="form-input form-control required" style="height: 2rem">
                                        @foreach ($pc as $item)
                                            <option value="{{ $item->id }}" {{ $project->pc_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="sales_id">Sales</label>
                                    <select id="sales_id" name="sales_id" class="form-input form-control required" style="height: 2rem">
                                        @foreach ($sls as $item)
                                            <option value="{{ $item->id }}" {{ $project->sales_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="xtype">Type</label>
                                    <select id="select2" id="xtype" name="xtype" class="form-input form-control required" style="height:2rem">
                                            <option value="{{ $project->xtype }}" hidden>{{$project->xtype}}</option>
                                            <option value="Project">Project</option>
                                        <option value="Manage Service">Manage Service</option>
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea id="description" class="form-input form-control required" name="description" required>{{ $project->description }}</textarea>
                                </div>
                            </div>


                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" id="btn-update"
                                    class="btn btn-primary me-1 mb-1">Submit</button>
                                <button type="button" class="btn btn-warning me-1 mb-1"
                                    onclick="renderView(`{!! route('master-project.project') !!}`)">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </section>

</div>
@prepend('after-script')

<script type="text/javascript" src="{{ asset('js/module/masterdata/project/project.js') }}"></script>
