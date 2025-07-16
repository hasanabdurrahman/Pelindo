<div class="card my-5">
    {{-- @foreach (json_decode($data['phase']) as $item) --}}
        <section class="section">
            <div class="container" style="height: auto">
            
            </div>
        </section>

        {{-- @php
            $items = json_encode($item,true);
        @endphp --}}
        <script>
            var res_data = JSON.parse(`{!!$data['phase']!!}`)
            var options = {
                series: [
                    {
                        data: res_data
                    }
                ],
                chart: {
                    height: 'auto',
                    type: 'rangeBar'
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        distributed: true,
                        dataLabels: {
                            hideOverflowingLabels: false,
                        },
                    }
                },
                xaxis: {
                    type: 'datetime',
                    labels: {
                        style: {
                            fontSize: '15px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            fontSize: '15px'
                        }
                    }
                },
                grid: {
                    row: {
                        colors: ['#f3f4f5', '#fff'],
                        opacity: 1
                    }
                },
            };
            
            var chart = new ApexCharts(document.querySelector(`.container`), options);
            chart.render();
        </script>
    {{-- @endforeach --}}
    {{-- @foreach (json_decode($data['phase']) as $parent => $items)
        <div class="card-header bg-light-primary py-2" style="cursor: pointer" onclick="expandChart('#body-{{str_replace(' ', '', $parent)}}')">
            {{$parent}}
        </div>
        <div class="card-body" id="body-{{str_replace(' ', '', $parent)}}" style="display: none;">
            <div id="chart-{{str_replace(' ', '', $parent)}}">
            </div>
        </div>

        @php
            $items = json_encode($items,true);
        @endphp
        <script>
            var res_data = JSON.parse(`{!!$items!!}`)
            var options = {
                series: [
                {
                    data: res_data
                }
                ],
                    chart: {
                    height: 150,
                    type: 'rangeBar'
                },
                    plotOptions: {
                    bar: {
                        horizontal: true,
                        distributed: true,
                        dataLabels: {
                            hideOverflowingLabels: false
                        }
                    }
                },
                xaxis: {
                    type: 'datetime'
                },
                grid: {
                    row: {
                        colors: ['#f3f4f5', '#fff'],
                        opacity: 1
                    }
                }
            };
            
            var parent = "{{$parent}}"
            parent = parent.replace(' ', '')
            var chart = new ApexCharts(document.querySelector(`#chart-${parent}`), options);
            chart.render();
        </script>
    @endforeach --}}

    {{-- <div class="card-footer bg-light-primary my-0 py-0">
        <small>*klik judul fase untuk melihat chart</small>
    </div> --}}
</div>
