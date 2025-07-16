@foreach (json_decode($data['phase']) as $parent => $items)
    <div class="card my-5">
        <div class="card-header bg-light-primary py-2" style="cursor: pointer" onclick="expandChart(this)">
            {{$parent}}
        </div>
        <div class="card-body" style="display: none;">
            <div id="chart-{{$parent}}">
            </div>
        </div>
        <div class="card-footer bg-light-primary my-0 py-0">
            <small>*klik judul fase untuk melihat chart</small>
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
    
        var chart = new ApexCharts(document.querySelector("#chart-{{$parent}}"), options);
        chart.render();
    </script>
@endforeach
