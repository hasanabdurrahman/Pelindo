function changeTab(timelineExists) {
    showLeftNav(timelineExists)
    $('#action-button2-2').fadeOut()
    if (!timelineExists) {
        $("#container-project-list-2").fadeOut();
        $("#container-timeline-2").fadeOut();
        $("#action-button-2").fadeOut();
        $("#container-project-list").fadeIn();
        $("#container-timeline").fadeIn();

        $("#project-timeline-container")
            .html(`<div class="d-flex justify-content-center align-items-center m-5">
                        <h6>*Harap pilih project dari list project di kiri layar</h6>
                    </div>`);
    } else {
        $("#container-project-list").fadeOut();
        $("#container-timeline").fadeOut();
        $("#action-button").fadeOut();
        $("#container-project-list-2").fadeIn();
        $("#container-timeline-2").fadeIn();
        $("#project-timeline-container-2")
            .html(`<div class="d-flex justify-content-center align-items-center m-5">
                        <h6>*Harap pilih project dari list project di kiri layar</h6>
                    </div>`);
    }
}

function renderTimeline(id, timelineExists) {
    var scndId = "";
    if (timelineExists) {
        scndId = "-2";
    }
    $("#container-btn-add" + scndId).fadeOut();
    $("#btn-edit" + scndId).fadeOut();
    $("#btn-approve" + scndId).fadeOut();

    apiCall(
        `master-project/timeline/getTimeline/${id}`,
        "GET",
        "",
        null,
        null,
        null,
        true,
        (res) => {
            $("#action-button" + scndId).fadeIn();
            $("#action-button2" + scndId).fadeIn();
            $(".loading").hide();
            $("#project-timeline-container" + scndId).html(res.blade);
            $("#id-project").html(btoa(id));

            if (res.data.timeline != null) {
                timelineDataTable(res.data.timeline.transactionnumber);
                const trans_numer = btoa(res.data.timeline.transactionnumber);
                if (res.data.timeline.approved_at == null) {
                    $("#btn-edit" + scndId).attr(
                        "onclick",
                        `renderView('${$('meta[name="baseurl"]').attr(
                            "content"
                        )}master-project/timeline/edit/${trans_numer}')`
                    );
                    $("#btn-edit" + scndId).fadeIn();
                    $("#btn-approve" + scndId).attr(
                        "onclick",
                        `approve('${res.data.timeline.id}')`
                    );
                    $("#btn-approve" + scndId).fadeIn();
                } else {
                    $("#container-btn-add" + scndId).fadeOut();
                    $("#btn-add" + scndId).fadeOut();
                    $("#btn-edit" + scndId).fadeOut();
                    $("#btn-approve" + scndId).fadeOut();
                }

                $(".btn-print").fadeIn();
                $("#export-excel").attr(
                    "href",
                    `/master-project/timeline/print/excel/${trans_numer}`
                );
                $("#export-pdf").attr(
                    "href",
                    `/master-project/timeline/print/pdf/${trans_numer}`
                );
            } else {
                const project_id = btoa(res.project.id);
                $("#btn-add" + scndId).attr(
                    "onclick",
                    `renderView('${$('meta[name="baseurl"]').attr(
                        "content"
                    )}master-project/timeline/add/${project_id}')`
                );
                $("#modal-template")
                    .find("#form-generate")
                    .find('input[name="project_id"]')
                    .val(project_id);
                $("#container-btn-add" + scndId).fadeIn();
                $("#btn-add" + scndId).fadeIn();
                $(".btn-print").fadeOut();
            }
        }
    );
}

function timelineDataTable(tn_number) {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    var table = $(".data-table").DataTable({
        responsive: true,
        scrollX: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: `${$('meta[name="baseurl"]').attr(
                "content"
            )}master-project/timeline/datatable`,
            method: "POST",
            data: function (data) {
                (data._token = `${$('meta[name="csrf-token"]').attr(
                    "content"
                )}`),
                    (data.tn_number = tn_number);
            },
        },
        columns: [
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                className: "text-center",
                width: "20px",
            },
            {
                data: "close_action",
                name: "close_action",
                className: "text-center",
            },
            {
                data: "status",
                name: "status",
                className: "text-center",
            },
            {
                data: "transactionnumber",
                name: "transactionnumber",
                className: "text-center",
            },
            {
                data: "detail",
                name: "detail",
                className: "text-center",
            },
            {
                data: "startdate",
                name: "startdete",
                className: "text-center",
            },
            {
                data: "enddate",
                name: "enddate",
                className: "text-center",
            },
            {
                data: "bobot",
                name: "bobot",
                className: "text-center",
            },
            {
                data: "karyawan",
                name: "karyawan",
                className: "text-center",
            },
        ],
    });
}

function setPhase() {
    $('select[name="timeline-type"]').removeClass("is-invalid");
    let timelineType = $('select[name="timeline-type"]').val();
    let project_id = $('input[name="project_id"]').val();
    project_id = btoa(project_id);

    if (timelineType != null) {
        apiCall(
            `master-project/timeline/set-phase/${timelineType}/${project_id}`,
            "GET",
            "",
            null,
            null,
            null,
            true,
            (res) => {
                $(".loading").hide();
                $("#choose-timeline-type").fadeOut();
                $("#btn-save").fadeIn();
                $("#bobotContainer").fadeIn();

                let html = res.blade;
                $("#phase-container").html(html);
                // res.data.map((v, k) => {
                //     html += res.blade
                //     $('#phase-container').html(html)
                // })

                const elForm = $("#phase-container").find(
                    'input[name="show_phase[]"]'
                );
                const phaseForm = $("#phase-container").find(
                    'input[name="phase[]"]'
                );
                for (let i = 0; i < elForm.length; i++) {
                    $(elForm[i]).val(res.data[i].name);
                    $(phaseForm[i]).val(res.data[i].name);
                }

                // $('.deletePhase').fadeOut()
                $(".select-employee").select2();
                $("#phase-container").fadeIn(750);
            }
        );
    } else {
        $('select[name="timeline-type"]').addClass("is-invalid");
        Toastify({
            text: `Harap pilih tipe timeline`,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            style: {
                background: "linear-gradient(to right, #ff5f6d, #ffc371)",
            },
        }).showToast();
    }
}

function addPhase(project_id) {
    apiCall(
        `master-project/timeline/render-form/${btoa(project_id)}`,
        "GET",
        "",
        null,
        null,
        null,
        true,
        (res) => {
            $(".loading").hide();
            $("#phase-container").append(res.blade);
            $(".select-employee").select2();
        }
    );
}

function addWork(el) {
    let parentEl = $(el).parent().parent().parent();
    $(".select-employee").select2("destroy");
    let duplicateForm = $(el).parent().parent().clone(true).appendTo(parentEl);
    $(duplicateForm).find(".btn-remove-work").fadeIn();

    // $(duplicateForm).find('.actionWork').append(`
    //     <a href='#' onclick="removeWork(this)" class='btn icon btn-sm btn-outline-danger rounded-pill'>
    //         <i class="bi bi-trash-fill"></i>
    //     </a>
    // `)

    $(".select-employee").select2();
}

function removeWork(el) {
    Swal.fire({
        title: "Apakah anda yakin ingin menghapus baris ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Batal",
        confirmButtonText: "Ya, Hapus baris",
    }).then((result) => {
        if (result.isConfirmed == true) {
            $(el).parent().parent().remove();
        }
    });
}

function removePhase(el) {
    Swal.fire({
        title: "Apakah anda yakin ingin menghapus phase ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Batal",
        confirmButtonText: "Ya, Hapus phase",
    }).then((result) => {
        console.log(result);
        if (result.isConfirmed == true) {
            $(el).parent().parent().parent().next().remove();
            $(el).parent().parent().parent().remove();
        }
    });
}

function cancel() {
    Swal.fire({
        title: "Apakah anda ingin kembali atau mengganti jenis timeline?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: "Kembali ke Halaman Awal",
        confirmButtonText: "Ganti Timeline",
    }).then((result) => {
        if (result.isConfirmed == true) {
            $("#phase-container").html("");
            $("#phase-container").fadeOut();
            $("#btn-save").fadeOut(function () {
                $(this).attr("style", "display: none !important");
            });
            $("#choose-timeline-type").fadeIn(750);
            $("#bobotTotal").html("0");
            $("#bobotContainer").fadeOut();
        } else {
            renderView(
                `${$('meta[name="baseurl"]').attr(
                    "content"
                )}master-project/timeline`
            );
        }
    });
}

function changeVal(el) {
    if ($(el).hasClass("is-invalid") && $(el).val() != "") {
        $(el).removeClass("is-invalid");
    } else if ($(el).val() == "") {
        $(el).addClass("is-invalid");
    }

    // Set Min Date on end_date
    if ($(el).attr("type") == "date" && $(el).attr("name") == "start_date[]") {
        const minEndDate = $(el).val();
        $(el)
            .parent()
            .parent()
            .find('input[name="end_date[]"]')
            .attr("min", minEndDate)
            .prop("disabled", false);
        if (minEndDate == "") {
            $(el)
                .parent()
                .parent()
                .find('input[name="end_date[]"]')
                .prop("disabled", true)
                .val("");
        }
    }

    // Set Phase hidden value
    if ($(el).attr("name") == "show_phase[]") {
        $(el)
            .parent()
            .parent()
            .parent()
            .find('input[name="phase[]"]')
            .val($(el).val());
    }

    // Set Document Hidden Value
    if ($(el).attr("name") == "document_check[]") {
        if ($(el).is(":checked")) {
            $(el).parent().find('input[name="is_document[]"]').val("1");
        } else {
            $(el).parent().find('input[name="is_document[]"]').val("0");
        }
    }
}

function countBobot(el) {
    // Show Total Bobot
    let totalBobot = 0;
    $('input[name="bobot[]"]').each(function (k, v) {
        if ($(v).val() != "") {
            totalBobot += parseInt($(v).val());
        }
    });

    $("#bobotTotal").html(`${totalBobot}`);
    if (totalBobot > 100) {
        $("#bobotTotal").removeAttr("class").addClass("text-danger");
    } else if (totalBobot == 100) {
        $("#bobotTotal").removeAttr("class").addClass("text-success");
    } else {
        $("#bobotTotal").removeAttr("class").addClass("text-black");
    }
}


$(document).on("click", ".close_action", function () {
    var id = $(this).data('id');
    // alert(id);
    prompt("approve", "Close Timeline", (confirm) => {
        if (confirm) {
            apiCall(
                "master-project/timeline/close_action/" + id,
                "GET",
                "",
                null,
                null,
                (err) => {
                    console.log(err);
                    $(".loading").hide();
                    Toastify({
                        text: `Gagal close data timeline, ${err.statusText}`,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        style: {
                            background:
                                "linear-gradient(to right, #ff5f6d, #ffc371)",
                        },
                    }).showToast();
                },
                true,
                (res) => {
                    console.log(res);
                    $(".loading").hide();
                    Toastify({
                        text: `Berhasil close data timeline`,
                        duration: 1000,
                        close: true,
                        gravity: "top",
                        callback: function () {
                            renderView(
                                `${$('meta[name="baseurl"]').attr(
                                    "content"
                                )}master-project/timeline`
                            );
                            $(
                                `v-pills-${$(
                                    'input[name="project_id"]'
                                ).val()}-tab`
                            ).trigger("click");
                        },
                        position: "right",
                        style: {
                            background:
                                "linear-gradient(to right, #00b09b, #96c93d)",
                        },
                    }).showToast();
                }
            ); 
        }
    })
    
});
$("#btn-save").on("click", function () {
    prompt("submit", "Timeline", (confirm) => {
        if (confirm) {
            const fAddComponent = $("#add-timeline");
            var required = fAddComponent.find(".required");
            var canInput = true;
            let totalBobot = 0;

            required.removeClass("is-invalid");

            // Form Validation
            for (var i = 0; i < required.length; i++) {
                if (required[i].value == "") {
                    canInput = false;
                    fAddComponent
                        .find(`input[name="${required[i].name}"]`)
                        .addClass("is-invalid");
                    fAddComponent
                        .find(`select[name="${required[i].name}"]`)
                        .addClass("is-invalid");
                    fAddComponent
                        .find(`textarea[name="${required[i].name}"]`)
                        .addClass("is-invalid");
                }

                if (required[i].name == "bobot[]") {
                    totalBobot += parseInt(required[i].value);
                }
            }

            if (totalBobot != 100) {
                let text = totalBobot > 100 ? "lebih" : "kurang";
                fAddComponent
                    .find(`input[name="bobot[]"]`)
                    .addClass("is-invalid");
                Toastify({
                    text: `Total bobot keseluruhan harus 100`,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    style: {
                        background:
                            "linear-gradient(to right, #ff5f6d, #ffc371)",
                    },
                }).showToast();

                return false;
            }

            if (canInput == false) {
                Toastify({
                    text: `Masih ada data yang kosong, harap lengkapi semua data terlebih dahulu`,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    style: {
                        background:
                            "linear-gradient(to right, #ff5f6d, #ffc371)",
                    },
                }).showToast();
            } else if (canInput == true) {
                apiCall(
                    "master-project/timeline/store",
                    "POST",
                    "add-timeline",
                    {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    null,
                    (err) => {
                        console.log(err);
                        $(".loading").hide();
                        Toastify({
                            text: `Gagal simpan data timeline, ${err.statusText}`,
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            style: {
                                background:
                                    "linear-gradient(to right, #ff5f6d, #ffc371)",
                            },
                        }).showToast();
                    },
                    true,
                    (res) => {
                        console.log(res);
                        $(".loading").hide();
                        Toastify({
                            text: `Berhasil simpan data timeline`,
                            duration: 1000,
                            close: true,
                            gravity: "top",
                            callback: function () {
                                renderView(
                                    `${$('meta[name="baseurl"]').attr(
                                        "content"
                                    )}master-project/timeline`
                                );
                                $(
                                    `v-pills-${$(
                                        'input[name="project_id"]'
                                    ).val()}-tab`
                                ).trigger("click");
                            },
                            position: "right",
                            style: {
                                background:
                                    "linear-gradient(to right, #00b09b, #96c93d)",
                            },
                        }).showToast();
                    }
                );
            }
        }
    });
});

function showLeftNav(timelineExists) {
    var scndId = "";
    if (timelineExists) {
        scndId = "-2";
    }
    $("#container-project-list" + scndId).animate({ width: "toggle" }, 350);
    setTimeout(() => {
        if ($("#container-project-list" + scndId).css("display") == "none") {
            $("#container-timeline" + scndId)
                .removeClass("col-md-9")
                .addClass("col-md-12");
            $("#action-button2" + scndId)
                .children()
                .removeClass("fa-chevron-left")
                .addClass("fa-chevron-right");
        }
    }, 400);

    if ($("#container-project-list" + scndId).css("display") != "none") {
        $("#container-timeline" + scndId).removeClass("col-md-12").addClass("col-md-9");
        $("#action-button2" + scndId)
            .children()
            .removeClass("fa-chevron-right")
            .addClass("fa-chevron-left");
    }
}

function changeProject() {
    if ($("#currentProject").css("display") == "none") {
        $("#changeProject").fadeOut();
        $("#currentProject").fadeIn();
    } else {
        $('select[name="new_project_id"]').select2({
            theme: "bootstrap-5",
            placeholder: "Pilih Project",
            allowClear: true,
        });
        $("#changeProject").fadeIn();
        $("#currentProject").fadeOut();
    }
}

function changePerson(el) {
    if (
        $(el)
            .parent()
            .parent()
            .parent()
            .parent()
            .find("#default-emp")
            .css("display") == "none"
    ) {
        $(el).parent().parent().parent().parent().find("#form-emp").fadeOut();
        $(el).parent().parent().parent().parent().find("#default-emp").fadeIn();
        $(el)
            .parent()
            .parent()
            .parent()
            .parent()
            .find(".select-employee")
            .removeClass("required");
    } else {
        console.log(
            $(el).parent().parent().parent().parent().find(".select-employee")
        );

        $(el)
            .parent()
            .parent()
            .parent()
            .parent()
            .find("#default-emp")
            .fadeOut();
        $(el).parent().parent().parent().parent().find("#form-emp").fadeIn();
        $(el)
            .parent()
            .parent()
            .parent()
            .parent()
            .find(".select-employee")
            .select2();
        $(el)
            .parent()
            .parent()
            .parent()
            .parent()
            .find(".select-employee")
            .addClass("required");
    }
}

$("#btn-update").on("click", function () {
    prompt("update", "Timeline", (confirm) => {
        if (confirm) {
            const fAddComponent = $("#edit-timeline");
            var required = fAddComponent.find(".required");
            var canInput = true;
            let totalBobot = 0;

            required.removeClass("is-invalid");

            // Form Validation
            for (var i = 0; i < required.length; i++) {
                if (required[i].value == "") {
                    canInput = false;
                    fAddComponent
                        .find(`input[name="${required[i].name}"]`)
                        .addClass("is-invalid");
                    fAddComponent
                        .find(`select[name="${required[i].name}"]`)
                        .addClass("is-invalid");
                    fAddComponent
                        .find(`textarea[name="${required[i].name}"]`)
                        .addClass("is-invalid");
                    console.log(required[i].name);
                }

                if (required[i].name == "bobot[]") {
                    totalBobot += parseInt(required[i].value);
                }
            }

            if (totalBobot != 100) {
                let text = totalBobot > 100 ? "lebih" : "kurang";
                fAddComponent
                    .find(`input[name="bobot[]"]`)
                    .addClass("is-invalid");
                Toastify({
                    text: `Total bobot keseluruhan tidak boleh ${text} dari 100`,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    style: {
                        background:
                            "linear-gradient(to right, #ff5f6d, #ffc371)",
                    },
                }).showToast();

                return false;
            }

            if (canInput == false) {
                Toastify({
                    text: `Masih ada data yang kosong, harap lengkapi semua data terlebih dahulu`,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    style: {
                        background:
                            "linear-gradient(to right, #ff5f6d, #ffc371)",
                    },
                }).showToast();
            } else if (canInput == true) {
                apiCall(
                    "master-project/timeline/update",
                    "POST",
                    "edit-timeline",
                    {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    null,
                    (err) => {
                        console.log(err);
                        $(".loading").hide();
                        Toastify({
                            text: `Gagal simpan data timeline, ${err.statusText}`,
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            style: {
                                background:
                                    "linear-gradient(to right, #ff5f6d, #ffc371)",
                            },
                        }).showToast();
                    },
                    true,
                    (res) => {
                        console.log(res);
                        $(".loading").hide();
                        Toastify({
                            text: `Berhasil simpan data timeline`,
                            duration: 1000,
                            close: true,
                            gravity: "top",
                            callback: function () {
                                renderView(
                                    `${$('meta[name="baseurl"]').attr(
                                        "content"
                                    )}master-project/timeline`
                                );
                                $(
                                    `v-pills-${$(
                                        'input[name="project_id"]'
                                    ).val()}-tab`
                                ).trigger("click");
                            },
                            position: "right",
                            style: {
                                background:
                                    "linear-gradient(to right, #00b09b, #96c93d)",
                            },
                        }).showToast();
                    }
                );
            }
        }
    });
});

function expandChart(cardBody) {
    if ($(cardBody).css("display") == "none") {
        $(cardBody).slideDown();
    } else {
        $(cardBody).slideUp();
    }
}

function approve(id) {
    id = btoa(id);
    prompt("approve", "Timeline", (confirm) => {
        if (confirm) {
            apiCall(
                `master-project/timeline/approve/${id}`,
                "GET",
                "",
                null,
                null,
                (err) => {
                    console.log(err);
                    $(".loading").hide();
                    Toastify({
                        text: `Gagal approve data timeline, ${err.statusText}`,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        style: {
                            background:
                                "linear-gradient(to right, #ff5f6d, #ffc371)",
                        },
                    }).showToast();
                },
                true,
                (res) => {
                    $(".loading").hide();
                    Toastify({
                        text: `Berhasil approve data timeline`,
                        duration: 1000,
                        close: true,
                        gravity: "top",
                        callback: function () {
                            renderView(
                                `${$('meta[name="baseurl"]').attr(
                                    "content"
                                )}master-project/timeline`
                            );
                            $(
                                `v-pills-${$(
                                    'input[name="project_id"]'
                                ).val()}-tab`
                            ).trigger("click");
                        },
                        position: "right",
                        style: {
                            background:
                                "linear-gradient(to right, #00b09b, #96c93d)",
                        },
                    }).showToast();
                }
            );
        }
    });
}

function exportFile(type, tn_number) {
    apiCall(
        `master-project/timeline/print/${type}/${tn_number}`,
        "GET",
        "",
        null,
        null,
        (err) => {
            console.log(err);
            $(".loading").hide();
            Toastify({
                text: `Gagal approve data timeline, ${err.statusText}`,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                style: {
                    background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                },
            }).showToast();
        },
        true,
        (res) => {
            $(".loading").hide();
            Toastify({
                text: `Berhasil approve data timeline`,
                duration: 1000,
                close: true,
                gravity: "top",
                callback: function () {
                    renderView(
                        `${$('meta[name="baseurl"]').attr(
                            "content"
                        )}master-project/timeline`
                    );
                    $(
                        `v-pills-${$('input[name="project_id"]').val()}-tab`
                    ).trigger("click");
                },
                position: "right",
                style: {
                    background: "linear-gradient(to right, #00b09b, #96c93d)",
                },
            }).showToast();
        }
    );
}

$("#btn-template").on("click", function () {
    $("#modal-template").modal("toggle");
});

$("#form-generate").on("submit", function () {
    const fAddComponent = $("#form-generate");
    var required = fAddComponent.find(".required");
    var canInput = true;

    required.removeClass("is-invalid");

    // Form Validation
    for (var i = 0; i < required.length; i++) {
        if (required[i].value == "") {
            canInput = false;
            fAddComponent
                .find(`input[name="${required[i].name}"]`)
                .addClass("is-invalid");

            Toastify({
                text: `Form ${form_name} is Required`,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                style: {
                    background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                },
            }).showToast();
        }
    }

    if (canInput) {
        apiCall(
            "master-project/timeline/generate-template",
            "POST",
            "form-generate",
            { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
            null,
            (err) => {
                console.log(err);
                $(".loading").hide();
                Toastify({
                    text: `Gagal Generate Template Timeline, ${err.statusText}`,
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    style: {
                        background:
                            "linear-gradient(to right, #ff5f6d, #ffc371)",
                    },
                }).showToast();
            },
            true,
            (res) => {
                $(".loading").hide();
                // $('#modal-template').modal('toggle')

                var tempDownload = document.createElement("a");
                tempDownload.style.display = "none";
                document.body.appendChild(tempDownload);
                tempDownload.setAttribute(
                    "href",
                    `/master-project/timeline/download-file/${res.file}`
                );
                tempDownload.setAttribute("download", res.file);

                tempDownload.click();

                Toastify({
                    text: `Berhasil Generate Template`,
                    duration: 1000,
                    close: true,
                    gravity: "top",
                    callback: function () {
                        // renderView(`${$('meta[name="baseurl"]').attr('content')}master-project/timeline`)
                        byPassGenerateTemplate();
                    },
                    position: "right",
                    style: {
                        background:
                            "linear-gradient(to right, #00b09b, #96c93d)",
                    },
                }).showToast();
            }
        );
    }
});

function byPassGenerateTemplate() {
    $("#choose-timeline-type").fadeOut();
    setTimeout(() => {
        $("#form-import-container").fadeIn();
    }, 750);
}

function cancelImport() {
    $("#form-import-container").fadeOut();
    setTimeout(() => {
        $("#choose-timeline-type").fadeIn();
    }, 750);
}

$("#form-import").on("submit", function () {
    const fAddComponent = $("#form-import");
    var formData = new FormData(fAddComponent[0]);
    var required = fAddComponent.find(".required");
    var canInput = true;

    required.removeClass("is-invalid");

    // Form Validation
    for (var i = 0; i < required.length; i++) {
        if (required[i].value == "") {
            canInput = false;
            fAddComponent
                .find(`input[name="${required[i].name}"]`)
                .addClass("is-invalid");
            var form_name = required[i].id.replace("_", " ").toUpperCase();

            Toastify({
                text: `Form ${form_name} is Required`,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "right",
                style: {
                    background: "linear-gradient(to right, #ff5f6d, #ffc371)",
                },
            }).showToast();
        }
    }

    if (canInput) {
        $.ajax({
            url: `${$('meta[name="baseurl"]').attr(
                "content"
            )}master-project/timeline/import-timeline`,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: () => {
                beforeAjaxSend();
            },
            error: function (err) {
                $(".loading").hide();
                onAjaxError(err);
            },
            success: function (res) {
                $(".loading").hide();

                $("#modal-template").modal("toggle");
                Toastify({
                    text: `Berhasil import data timeline`,
                    duration: 1000,
                    close: true,
                    gravity: "top",
                    callback: function () {
                        renderView(
                            `${$('meta[name="baseurl"]').attr(
                                "content"
                            )}master-project/timeline`
                        );
                    },
                    position: "right",
                    style: {
                        background:
                            "linear-gradient(to right, #00b09b, #96c93d)",
                    },
                }).showToast();
            },
        });
    }
});
