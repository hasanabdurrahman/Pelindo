<div class="card-header">
    List Project
</div>
<div class="card-body">
    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
        @foreach ($projects as $project)
            @if ($project->has_timeline == 0)
                <a class="nav-link" href="#" id="v-pills-{{ $project->id }}-tab" role="tab"
                    aria-controls="v-pills-{{ $project->id }}" aria-selected="false" tabindex="-1"
                    onclick="renderTimeline('{{ base64_encode($project->id) }}', false)">{{ $project->name }}
                    ({{ $project->code }})</a>
            @endif
        @endforeach
    </div>
</div>
<div class="card-footer">
    {{-- {{ $projects->links() }} --}}
</div>

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
