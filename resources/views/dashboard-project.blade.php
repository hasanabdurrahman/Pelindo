<div id="render">
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Dashboard Project <i class="fas fa-refresh refresh-page"
                            onclick="renderView(`{!! route('dashboard.project') !!}`)"></i></h3>
                    <p class="text-subtitle text-muted"></p>
                </div>
            </div>
        </div>
    </div>

    <section class="section row">
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Total Project Tahun {{ \Carbon\Carbon::now()->subYear()->format('Y') }}
                    </div>
                </div>
                <div class="card-body">
                    <div class="" id="total-project" style="width: 100%; height: 20rem"></div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Project Berdasarkan Jenis
                    </div>
                </div>
                <div class="card-body">
                    <div class="" id="project-by-jenis" style="width: 100%; height: 20rem"></div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        Project Berdasarkan Client
                    </div>
                </div>
                <div class="card-body">
                    <div class="" id="project-by-client" style="width: 100%; height: 30rem"></div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        List Project
                    </div>
                </div>
                <div class="card-body" id="list-project">
                    @include('includes.card-project-dashboard')
                </div>
            </div>
        </div>
    </section>
</div>

@prepend('after-script')
    <script src="{{ asset('assets/extensions/amcharts/core.js') }}"></script>
    <script src="{{ asset('assets/extensions/amcharts/charts.js') }}"></script>
    <script src="{{ asset('assets/extensions/amcharts/themes/animated.js') }}"></script>

    <!-- chart Tahun -->
    <script>
        $('#total-project').html('')
        am4core.disposeAllCharts();
        am4core.ready(function() {
            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            var chart = am4core.create("total-project", am4charts.XYChart);
            chart.paddingRight = 20;

            var data = `{!! $data['totalProject'] !!}`;
            data = JSON.parse(data)
            for (let i = 0; i < data.length; i++) {
                data[i].date = new Date(data[i].date)
            }
            chart.data = data;

            var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
            dateAxis.renderer.grid.template.location = 0;
            dateAxis.renderer.axisFills.template.disabled = true;
            dateAxis.renderer.ticks.template.disabled = true;
            dateAxis.renderer.labels.template.fill = am4core.color("#C0C0C0");

            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.tooltip.disabled = true;
            valueAxis.renderer.minWidth = 35;
            valueAxis.renderer.axisFills.template.disabled = true;
            valueAxis.renderer.ticks.template.disabled = true;
            valueAxis.renderer.labels.template.fill = am4core.color("#C0C0C0");

            var series = chart.series.push(new am4charts.LineSeries());
            series.dataFields.dateX = "date";
            series.dataFields.valueY = "value";
            series.strokeWidth = 2;
            series.tooltipText = "Total Project: {valueY}";

            // set stroke property field
            series.propertyFields.stroke = "color";

            chart.cursor = new am4charts.XYCursor();

            var scrollbarX = new am4core.Scrollbar();
            chart.scrollbarX = scrollbarX;

            chart.logo.disabled = true;
        });
    </script>

    <!-- Chart Jenis -->
    <script>
        am4core.ready(function() {
            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            var chart = am4core.create('project-by-jenis', am4charts.XYChart)
            chart.colors.step = 2;

            chart.legend = new am4charts.Legend()
            chart.legend.position = 'top'
            chart.legend.paddingBottom = 20
            chart.legend.labels.template.maxWidth = 95
            chart.cursor = new am4charts.XYCursor();
            chart.logo.disabled = true;

            var scrollbarX = new am4core.Scrollbar();
            chart.scrollbarX = scrollbarX;

            var xAxis = chart.xAxes.push(new am4charts.CategoryAxis())
            xAxis.dataFields.category = 'category'
            xAxis.renderer.cellStartLocation = 0.1
            xAxis.renderer.cellEndLocation = 0.9
            xAxis.renderer.grid.template.location = 0;
            xAxis.renderer.labels.template.fill = am4core.color("#C0C0C0");

            var yAxis = chart.yAxes.push(new am4charts.ValueAxis());
            yAxis.min = 0;
            yAxis.renderer.labels.template.fill = am4core.color("#C0C0C0");

            function createSeries(value, name) {
                var series = chart.series.push(new am4charts.ColumnSeries())
                series.dataFields.valueY = value
                series.dataFields.categoryX = 'category'
                series.name = name

                series.events.on("hidden", arrangeColumns);
                series.events.on("shown", arrangeColumns);

                var bullet = series.bullets.push(new am4charts.LabelBullet())
                bullet.interactionsEnabled = false
                bullet.dy = 30;
                bullet.label.text = '{valueY}'
                bullet.label.fill = am4core.color('#ffffff')

                return series;
            }

            var data = `{!! $data['projectByJenis'] !!}`;
            data = JSON.parse(data)
            chart.data = data
            chart.legend.labels.template.fill = am4core.color("#C0C0C0");
            chart.legend.valueLabels.template.fill = am4core.color("#C0C0C0");

            createSeries('project', 'Project');
            createSeries('manage_service', 'Manage Service');

            function arrangeColumns() {

                var series = chart.series.getIndex(0);

                var w = 1 - xAxis.renderer.cellStartLocation - (1 - xAxis.renderer.cellEndLocation);
                if (series.dataItems.length > 1) {
                    var x0 = xAxis.getX(series.dataItems.getIndex(0), "categoryX");
                    var x1 = xAxis.getX(series.dataItems.getIndex(1), "categoryX");
                    var delta = ((x1 - x0) / chart.series.length) * w;
                    if (am4core.isNumber(delta)) {
                        var middle = chart.series.length / 2;

                        var newIndex = 0;
                        chart.series.each(function(series) {
                            if (!series.isHidden && !series.isHiding) {
                                series.dummyData = newIndex;
                                newIndex++;
                            } else {
                                series.dummyData = chart.series.indexOf(series);
                            }
                        })
                        var visibleCount = newIndex;
                        var newMiddle = visibleCount / 2;

                        chart.series.each(function(series) {
                            var trueIndex = chart.series.indexOf(series);
                            var newIndex = series.dummyData;

                            var dx = (newIndex - trueIndex + middle - newMiddle) * delta

                            series.animate({
                                property: "dx",
                                to: dx
                            }, series.interpolationDuration, series.interpolationEasing);
                            series.bulletsContainer.animate({
                                property: "dx",
                                to: dx
                            }, series.interpolationDuration, series.interpolationEasing);
                        })
                    }
                }
            }

        }); // end am4core.ready()
    </script>

    <!-- Chart By Client -->
    <script>
        am4core.ready(function() {
            // Themes begin
            am4core.useTheme(am4themes_animated);
            // Themes end

            var chart = am4core.create("project-by-client", am4charts.PieChart);
            chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

            var data = `{!! $data['projectByClient'] !!}`;
            data = JSON.parse(data)
            chart.data = data
            chart.radius = am4core.percent(70);
            chart.innerRadius = am4core.percent(40);
            chart.startAngle = 180;
            chart.endAngle = 360;
            chart.logo.disabled = true;

            var series = chart.series.push(new am4charts.PieSeries());
            series.dataFields.value = "total";
            series.dataFields.category = "client_name";
            series.labels.template.disabled = false;
            series.labels.template.fontSize = 12;
            series.labels.template.fill = am4core.color('#C0C0C0');
            series.ticks.template.disabled = false;

            series.slices.template.cornerRadius = 10;
            series.slices.template.innerCornerRadius = 7;
            series.slices.template.draggable = false;
            series.slices.template.inert = true;
            series.alignLabels = false;

            series.hiddenState.properties.startAngle = 90;
            series.hiddenState.properties.endAngle = 90;

            // chart.legend = new am4charts.Legend();
            // chart.legend.maxHeight = 80;
            // chart.legend.maxWidth = undefined;
            // chart.legend.scrollable = true;

        }); // end am4core.ready()
    </script>
