<style>
    .square-image {
        width: 250px; /* Lebar maksimal 100% */
        height: auto; /* Tinggi akan disesuaikan dengan lebar */
        max-height: 250px%; /* Tinggi maksimal 100% */
        object-fit: cover; /* Memastikan gambar selalu terlihat dengan benar */
    }
</style>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Edit Tasklist<i class="fas fa-refresh refresh-page" onclick="renderView(`{!!route('tasklist.edit', $tasklist->id)!!}`)"></i> </h3>
                <p class="text-subtitle text-muted"></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">Transaction</a></li>
                        <li class="breadcrumb-item spa_route" aria-current="page"><a href="javascript:void(0)" onclick="renderView(`{!!route('transaction.tasklist')!!}`)">Tasklist</a></li>
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
                <form class="form form-vertical" id="edit-tasklist" action="javascript:void(0)">
                    @csrf
                    <div class="form-body">
                        <input type="hidden" name="id" value="{{$tasklist->id}}" class="form-input">
                        <input type="hidden" id="karyawan_id" class="form-input form-control required" name="karyawan_id" value='{{auth()->user()->id}}' disabled>
                        @method('post')
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="transactionnumber">Transaction Number</label>
                                    <input type="text" id="transactionnumber" class="form-input form-control required" name="transactionnumber" value="{{$tasklist->transactionnumber}}"disabled>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="project_id">Project</label>
                                    <select id="project_id" name="project_id" class="form-input form-control required" style="height: 2rem">
                                        @foreach ($project as $item)
                                            <option value="{{ $item->id }}" {{ $tasklist->project_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="timelineA_id">Timeline</label>
                                    <select id="timelineA_id" name="timelineA_id" class="form-input form-control required" style="height: 2rem"> 
                                        @foreach ($filteredTimelineA as $item)
                                            <option value='{{ $item->id }}' {{ $tasklist->timelineA_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->detail }}
                                            </option>
                                        @endforeach
                                    </select>   
                                </div>

                                <div class="d-flex gap-2 align-items-center mb-2">
                                    <div class="form-check form-switch" style="padding-left: 0 !important">
                                        <span class="nav-link-text">Pekerjaan by Request Team</span>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input  me-0" type="checkbox" id="toggle-work">
                                        <label class="form-check-label"></label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="progress">Progress %</label>
                                    <input type="number" id="progress" class="form-input form-control" name="progress" placeholder=""value="{{$tasklist->progress}}">
                                </div>
                            </div>
                            <div class="col-md-12 col-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea type="text" id="description" class="form-input form-control required" name="description" placeholder="" value="{{$tasklist->description}}">{{$tasklist->description}}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12 col-12">
                                <div class="form-group">
                                    <label for="image" class="form-label">Upload File</label>
                                    <input class="form-input form-control" type="file" id="image" name="image[]" multiple>
                                </div>
                            </div>

                            <div class="col-md-12 col-12">
                                <div class="form-group">
                                    <label for="existing_images" class="form-label">Existing Images</label>
                                    <div id="existing_images">
                                        @php
                                        $images = !empty($tasklist->image) ? explode(',', $tasklist->image) : [];
                                        @endphp
                            
                                        @if(!empty($images))
                                            @foreach($images as $image)
                                                <img src="{{ asset('tasklist/' . $image) }}" alt="Image" class="square-image">
                                            @endforeach
                                        @else
                                            <p>No existing images available.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" id="btn-update" class="btn btn-primary me-1 mb-1">Save</button>
                                <button type="button" class="btn btn-warning me-1 mb-1" onclick="renderView(`{!!route('transaction.tasklist')!!}`)" >Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </section> 
    @prepend('after-script')
    <script type="text/javascript" src="{{ asset('js/module/transaction/tasklist/tasklist.js') }}"></script>
</div>