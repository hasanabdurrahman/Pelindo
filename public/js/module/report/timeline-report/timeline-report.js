$('input[name="startdate"]').on('change', function(e){
    let minEnd = new Date($(this).val())
    minEnd.setDate(minEnd.getDate() + 1)
    minEnd =  minEnd.getFullYear() + '-' + ('0' + (minEnd.getMonth()+1)).slice(-2) + '-' + ('0' + minEnd.getDate()).slice(-2)
                    
    $('input[name="enddate"]').attr('min', minEnd).val(minEnd)
})

$('select[name="filter"]').on('change', function(){
    if($(this).val() != ''){
        $('#search-form').fadeIn()
    } else {
        $('#search-form').fadeOut()
    }
})

$('#btn-preview').on('click', function(){
    $('#preview').fadeOut();
    let isValid = validation()
    if(isValid){
        // Call API to show preview
        apiCall('report/timeline-report/data/preview', 'POST', 'timeline-report', 
            {"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")},
        null, 
        null,
        true,
        (res) => {
            console.log(res)
            $('.loading').hide()
            const column = res.column
            const data = res.data

            let columnHtml = '<tr>';
            for (let i = 0; i < column.length; i++) {
                columnHtml += `<th>${column[i].replace('_', ' ')}</th>` 
            }
            columnHtml += '</tr>'
            $('#thead').html(columnHtml)

            let dataHtml = '';
            for (let x = 0; x < data.length; x++) {
                dataHtml += '<tr>'
                for (let y = 0; y < column.length; y++) {
                    dataHtml += `<td>${data[x][column[y]]}</td>`
                }
                dataHtml += '</tr>'
            }
            $('#tbody').html(dataHtml)

            $('#preview').fadeIn();
            $('.data-table').DataTable()
        })
    }
})

$('.btn-print').on('click', function(){
    let mode = $(this).data('mode')
    let isValid = validation()
    if(isValid){
        // Call API to show preview
        apiCall(`report/timeline-report/data/${mode}`, 'POST', 'timeline-report', 
            {"X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")},
        null, 
        null,
        true,
        (res) => {
            console.log(res)
            $('.loading').hide()
            const fileName = btoa(res.file)
            // window.open(`/report/timeline-report/download-file/${fileName}`, '_blank');
            window.location.href = `/report/timeline-report/print/${fileName}`
            setTimeout(() => {
                Toastify({
                    text: `Berhasil Mencetak Report Timeline`,
                    duration: 1000,
                    close:true,
                    gravity:"top",
                    callback: function() {
                        renderView(`${$('meta[name="baseurl"]').attr('content')}report/timeline-report`)
                    },
                    position: "right",
                    style: {
                        background: "linear-gradient(to right, #00b09b, #96c93d)",
                    }
    
                }).showToast();
            }, 1500);
        })
    }
})

function validation(){
    const fAddComponent = $('#timeline-report')
    var required = fAddComponent.find('.required')
    var canInput = true

    required.removeClass('is-invalid')

    // Form Validation
    for(var i = 0; i < required.length; i++){
        if (required[i].value == ''){
            canInput = false
            fAddComponent.find(`input[name="${required[i].name}"]`).addClass('is-invalid')
            fAddComponent.find(`select[name="${required[i].name}"]`).addClass('is-invalid')
            var form_name = required[i].id.replace('_', ' ').toUpperCase()
            Toastify({
                text: `Form ${form_name} is Required`,
                duration: 3000,
                close:true,
                gravity:"top",
                position: "right",
                style: {
                    background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                }
            }).showToast();
        }
    } 

    return canInput
}