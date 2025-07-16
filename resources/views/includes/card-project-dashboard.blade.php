@foreach ($data['allProject'] as $allProject)
    <div class="card" style="background-color: RGBA(103, 183, 220, 0.5)">
        <div class="card-body">
            <div class="card-title">
                <b>{{ $allProject->name }} ( {{ $allProject->contract_number }} )</b>
            </div>
            <div class="row">
                <div class="col-11 row align-items-start">
                    <div class="col-2"> Client </div>
                    <div class="col-1">:</div>
                    <div class="col-9"><span> {{ $allProject->client->name }} </span></div>
                </div>
            </div>

            <div class="row">
                <div class="col-11 row align-items-start">
                    <div class="col-2"> PC </div>
                    <div class="col-1">:</div>
                    <div class="col-9"><span> {{ $allProject->pc->name }} </span></div>
                </div>
            </div>

            <div class="row">
                <div class="col-11 row align-items-start">
                    <div class="col-2"> Sales </div>
                    <div class="col-1">:</div>
                    <div class="col-9"><span> {{ $allProject->sales->name }} </span></div>
                </div>
            </div>

            <div class="row">
                <div class="col-11 row align-items-start">
                    <div class="col-2"> Nilai Project </div>
                    <div class="col-1">:</div>
                    <div class="col-9"><span> <b> IDR {{ number_format($allProject->value, 2) }}
                                ({{ strtoupper(terbilang((int) $allProject->value)) }} RUPIAH)
                            </b> </span></div>
                </div>
            </div>

            @foreach ($data['progressProject'] as $progress)
                @if ($progress['id_project'] == $allProject->id)
                    <div class="col-12 col-md-3">
                        <h6>Progress</h6>
                    </div>
                    <div class="mt-2">
                        <h5 id="progress-label">{{ $progress['val'] }}%</h5>
                    </div>
                    <div class="progress">
                        <div id="progress-bar"
                            class="progress-bar progress-bar-striped {{(int)$progress['val'] < 50 ? 'bg-warning' : 'bg-success'}} progress-bar-animated"
                            role="progressbar" style="width: {{ ((int) $progress['val'] / 100) * 100 }}%"
                            aria-valuenow="{{ ((int) $progress['val'] / 100) * 100 }}" aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    {{-- <span>{{ $allProject->name }}</span> --}}
@endforeach

{!! $data['allProject']->links() !!}

<script>
    $('.pagination a').on('click', function(e) {
        e.preventDefault()
        let url = $(this).attr('href')
        $.ajax({
            url: url,
            method: 'GET',
            beforeSend: () => {
                $('#list-project').html(`
                    <div class="d-flex align-items-center justify-content-center py-5">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                `)
            },
            success: (res) => {
                $('#list-project').html(res)
            },
            error: (err) => {
                console.log(err)
                Toastify({
                    text: "Something went error!, Please check your internet connection and try again",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    style: {
                        background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                    }
                }).showToast();
            }
        })
    })

    $('.nav a').on('click', function(e) {
        e.preventDefault()
        $('.nav a').removeClass('active')
        $(this).addClass('active')
    })
</script>
