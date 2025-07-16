function renderMonitoring(id){
    // $('#btn-add').fadeOut()
    // $('#btn-edit').fadeOut()

    apiCall(`transaction/monitoring/getMonitoring/${id}`, 'GET', '', null, null, 
    null, 
    true, 
    (res) => {
        $('#action-button').fadeIn()
        $('#action-button2').fadeIn()
        $('.loading').hide()
        $('#project-monitoring-container').html(res.blade)
        $('#id-project').html(btoa(id))
        if (res.data.timeline != null) {
            calculateProgress(id);
            renderChart(id);
            monitoringDataTable(res.data.timeline.transactionnumber)
            monitoringDataTableall(res.data.timeline.transactionnumber)
            const trans_numer = btoa(res.data.timeline.transactionnumber)

            // Update nilai progress bar dengan nilai dari server
            // updateProgressBar(res.progressPercentage);
        } else {
            const project_id = btoa(res.project.id)
        }
    })
}

// Fungsi untuk mengambil data progress dari server
// function fetchProgress(id) {
//     apiCall(`transaction/monitoring/progress/${id}`, 'GET', '', null, null, 
//         null, 
//         true, 
//         (res) => {
//             // Panggil fungsi updateProgressBar dengan persentase yang diterima dari server
//             updateProgressBar(res.progressPercentage);
//         },
//         (error) => {
//             console.error('Error:', error);
//         }
//     );
// }

// Fungsi untuk memperbarui progress bar
// function updateProgressBar(percentage) {
//     var progressBar = document.getElementById("progress-bar");
//     var progressLabel = document.getElementById("progress-label");

//     // Periksa apakah 'percentage' adalah angka
//     if (!isNaN(percentage)) {
//         progressBar.style.width = percentage + "%";
//         progressBar.setAttribute("aria-valuenow", percentage);
//         progressLabel.innerText = percentage.toFixed(2) + "%";
//     } else {
//         // Tangani kesalahan jika 'percentage' bukan angka
//         console.error("Percentage is not a number:", percentage);
//     }
// }

// my_table
function monitoringDataTable(tn_number){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    var table = $('.data-table').DataTable({
        responsive: true,
        scrollX: true,
        processing: true,
        serverSide: true,
        pageLength: 10, // Menampilkan 10 data per halaman awal
        lengthMenu: [10, 25, 50, 75, 100], // Opsi untuk panjang tampilan halaman
        ajax: {
            url: `${$('meta[name="baseurl"]').attr('content')}transaction/monitoring/datatable`,
            method: "POST",
            data: function(data) {
                data._token = `${$('meta[name="csrf-token"]').attr('content')}`
                data.tn_number = tn_number
            },
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            className: 'text-center',
            width: '20px'
        },
        {
            data: 'nik',
            name: 'nik',
            className: 'text-center'
        },
        {
            data: 'role',
            name: 'role',
            className: 'text-center'
        },
        {
            data: 'start',
            name: 'start',
            className: 'text-center'
        },
        {
            data: 'contract',
            name: 'contract',
            className: 'text-center'
        },
        {
            data: 'fase',
            name: 'fase',
            className: 'text-center'
        },
        {
            data: 'detail',
            name: 'detail',
            className: 'text-center'
        },
        {
            data: 'karyawan',
            name: 'karyawan',
            className: 'text-center'
        },
    ]
    });
    
        // Menangani peristiwa klik pada tab "Table View"
    $("#table_view-tab").click(function() {
        table.ajax.reload(null, false);
    });
}
//team table
function monitoringDataTableall(tn_number){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var table_all;
    var table_all = $('.data-table-all').DataTable({
        responsive: true,
        scrollX: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: `${$('meta[name="baseurl"]').attr('content')}transaction/monitoring/datatableall`,
            method: "POST",
            data: function(data) {
                data._token = `${$('meta[name="csrf-token"]').attr('content')}`
                data.tn_number = tn_number
            },
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            className: 'text-center',
            width: '20px'
        },
        {
            data: 'nik',
            name: 'nik',
            className: 'text-center'
        },
        {
            data: 'role',
            name: 'role',
            className: 'text-center'
        },
        {
            data: 'start',
            name: 'start',
            className: 'text-center'
        },
        {
            data: 'contract',
            name: 'contract',
            className: 'text-center'
        },
        {
            data: 'fase',
            name: 'fase',
            className: 'text-center'
        },
        {
            data: 'detail',
            name: 'detail',
            className: 'text-center'
        },
        {
            data: 'status',
            name: 'status',
            className: 'text-center'
        },
        {
            data: 'karyawan',
            name: 'karyawan',
            className: 'text-center'
        },
    ]
    });
// Menangani peristiwa klik pada tab "Team Monitoring"
$("#team-tab").click(function() {
    table_all.ajax.reload(null, false);
});
}


function showLeftNav(){
    $('#container-project-list').animate({width:'toggle'},350)
    setTimeout(() => { 
        if($('#container-project-list').css('display') == 'none'){
            $('#container-monitoring').removeClass('col-md-9').addClass('col-md-12')
            $('#action-button2').children().removeClass('fa-chevron-left').addClass('fa-chevron-right')
        }
    }, 400);

    if($('#container-project-list').css('display') != 'none'){   
        $('#container-monitoring').removeClass('col-md-12').addClass('col-md-9')
        $('#action-button2').children().removeClass('fa-chevron-right').addClass('fa-chevron-left')
    }
}

function expandChart(el){
    $(el).parent().find('.card-body').slideDown()  
}

function calculateProgress(id) {
    $.ajax({
        url: `${$('meta[name="baseurl"]').attr('content')}transaction/monitoring/progress/${id}`,
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            // Panggil fungsi updateProgressBar dengan totalBobot yang diterima dari server
            updateProgressBar(res.totalBobot);
        },
        error: function (error) {
            console.error('Error:', error);
        }
    });
}

// Fungsi untuk memperbarui progress bar
function updateProgressBar(totalBobot) {
    var progressBar = document.getElementById("progress-bar");
    var progressLabel = document.getElementById("progress-label");

    // Periksa apakah 'totalBobot' adalah angka
    if (!isNaN(totalBobot)) {
        // Hitung persentase progress
        var percentage = (totalBobot / 100) * 100;

        // Update progress bar dengan persentase yang dihitung
        progressBar.style.width = percentage + "%";
        progressBar.setAttribute("aria-valuenow", percentage);
        progressLabel.innerText = percentage.toFixed(2) + "%";
    } else {
        // Tangani kesalahan jika 'totalBobot' bukan angka
        console.error("Total Bobot is not a number:", totalBobot);
    }
}

function renderChart(id) {
    $.ajax({
        url: `${$('meta[name="baseurl"]').attr('content')}transaction/monitoring/chart/${id}`,
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            if (res.progressData.length >= 0) {
                renderLineChart(res.progressData,res.dateRange);
            }
        },
        error: function (error) {
            console.error('Error:', error);
        }
    });
}

// monitoring.js
function renderChart(id) {
    $.ajax({
        url: `${$('meta[name="baseurl"]').attr('content')}transaction/monitoring/chart/${id}`,
        method: 'GET',
        dataType: 'json',
        success: function (res) {
            if (res.progressData.length >= 0) {
                renderLineChart(res.progressData, res.dateRange);
                // console.log('renderLineChart', progressData, dateRange);
            }
        },
        error: function (error) {
            console.error('Error:', error);
        }
    });
}

function renderLineChart(progressData, dateRange) {
    console.log('renderLineChart', progressData, dateRange);

    var options = {
        chart: {
            type: 'area',
            height: 280,
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        series: [
            {
                name: 'Total Progress',
                data: progressData,
            }
        ],
        xaxis: {
            type: 'datetime',
            categories: dateRange,
            labels: {
                format: 'yyyy-MM-dd', // Format: (Tahun-Bulan-Hari)
            },
            title: {
                text: 'Tanggal',
            },
        },
        yaxis: {
            title: {
                text: 'Progress',
            },
        },
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
}







