@foreach ($history as $item)
    <div class="card">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-md-9">
                <div class="row justify-content-center align-items-center">
                    <div class="col-12 col-md-1">
                        <div class="pr-50">
                            <div class="avatar">
                                @if ($item->employeeCreated != null)
                                    @if ($item->employeeCreated->picture)
                                        <img src="{{ Storage::url('employee/' . $item->employeeCreated->picture) }}" id="avatar-image">
                                    @else
                                        <img src="{{ asset('assets/images/faces/1.jpg') }}" id="avatar-image">
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-11">
                        <div class="row mt-2">
                            <div class="col-12">
                                <span class="badge bg-light-info">{{$item->created_by}}</span>
                            </div>
                            <div class="col-12">
                                <p>{{$item->notes}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                {{\Carbon\Carbon::parse($item->created_at)->translatedFormat('d, M Y H:i')}}
            </div>
        </div>
    </div>
@endforeach
