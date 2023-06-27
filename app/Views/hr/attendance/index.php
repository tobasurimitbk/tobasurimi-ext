<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<script src="<?= base_url(); ?>assets/js/webcam.js?v=<?= time(); ?>"></script>
<style type="text/css">
    .container1 {
        display: inline-block;
        width: 100%;
    }

    #Cam {
        background: rgb(255, 255, 215);
    }

    #Prev {
        background: rgb(255, 255, 155);
    }

    #Saved {
        background: rgb(255, 255, 55);
    }
</style>


<script language="JavaScript">
    function take_snapshot() {
        Webcam.snap(function(data_uri) {
            document.getElementById('results').innerHTML = '<img id="base64image" src="' + data_uri + '"/><button onclick="SaveSnap();">Save Snap</button>';
        });
    }

    function ShowCam() {
        Webcam.set({
            width: 420,
            height: 340,
            image_format: 'jpeg',
            jpeg_quality: 100
        });
        Webcam.attach('#my_camera');
    }

    function SaveSnap() {
        var file = document.getElementById("base64image").src;
        alert(file);
        var formdata = new FormData();
        formdata.append("base64image", file);
        alert(formdata);
        var ajax = new XMLHttpRequest();
        ajax.addEventListener("load", function(event) {
            uploadcomplete(event);
        }, false);
        ajax.open("POST", "/save-attendance");
        ajax.send(formdata);
    }

    function uploadcomplete(event) {
        var image_return = event.target.responseText;
        //var showup = document.getElementById("uploaded").src = image_return;
    }

    function GetAbsenIstirahat() {
        Webcam.snap(function(data_uri) {
            $.ajax({
                type: "post",
                url: "/save-attendance",
                data: {
                    employee_id: document.getElementById("employee_id").value,
                    state: 'ISTIRAHAT',
                    pic: data_uri
                },
                dataType: "json",
                success: function(response) {
                    alert('sukses');
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }

            });
        });

    }

    function GetAbsenKehadiran() {
        Webcam.snap(function(data_uri) {
            $.ajax({
                type: "post",
                url: "/save-attendance",
                data: {
                    employee_id: document.getElementById("employee_id").value,
                    state: 'KEHADIRAN',
                    pic: data_uri
                },
                dataType: "json",
                success: function(response) {
                    alert('sukses');
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }

            });
        });

    }

    function get_info(data) {
        $.ajax({
            type: "get",
            url: "/get-employee-by-company/" + data.value,
            data: {},
            dataType: "json",
            success: function(response) {
                document.getElementById("dnama").innerHTML = response.data.employeeName;
                document.getElementById("djabatan").innerHTML = response.data.divisionName;
                document.getElementById("djam_masuk").innerHTML = response.data.checkin;
                document.getElementById("distirahat_mulai").innerHTML = response.data.breakin;
                document.getElementById("distirahat_selesai").innerHTML = response.data.breakout;
                document.getElementById("djam_pulang").innerHTML = response.data.checkout;

                console.log(response);
                //alert('sukses');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }

        });

    }

    $(document).ready(function() {

        $(".btn-show-detail").click(function(e) {
            document.getElementById("pin").value = "";
            var temp = e.target.getAttribute('data-btn');
            if (temp == 'kehadiran') {
                document.getElementById("status_type").value = 'KEHADIRAN';
            } else {
                document.getElementById("status_type").value = 'ISTIRAHAT';
            }
            $(".detail-modal").modal("show")
        });

        $(".btn-hide-detail").click(function() {
            $(".detail-modal").modal("hide")
        });

        $(".btn-submit-pin").click(function() {
            $.ajax({
                type: "post",
                url: "/check-pin-employee",
                data: {
                    employee_id: document.getElementById("employee_id").value,
                    pin: document.getElementById("pin").value,
                },

                dataType: "json",
                success: function(response) {
                    if (response.message == 'PIN Validate!') {
                        Webcam.snap(function(data_uri) {
                            $.ajax({
                                type: "post",
                                url: "/save-attendance",
                                data: {
                                    employee_id: document.getElementById("employee_id").value,
                                    state: document.getElementById("status_type").value,
                                    pic: data_uri
                                },
                                dataType: "json",
                                success: function(response) {
                                    if (response.status) {
                                        Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                $(".detail-modal").modal("hide")
                                            })

                                    } else {
                                        Swal.fire({
                                                icon: 'error',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                $(".detail-modal").modal("hide")
                                            })
                                    }
                                },
                                error: function(xhr, ajaxOptions, thrownError) {
                                    //alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                                }

                            });
                        });

                    } else {

                    }
                    console.log(response);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    //csrf.val(response.token);
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }

            });

            $(".detail-modal").modal("hide")
        });


    })
    window.onload = ShowCam;
</script>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Attendance</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-center mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="container1" id="Cam">
                            <div id="my_camera"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                NIP
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select ar_id" name="employee_id" id="employee_id" onChange="get_info(this)">
                                        <option value=""></option>
                                        <?php
                                        if (!empty($dataEmployee)) {
                                            foreach ($dataEmployee as $employee) {
                                        ?>
                                                <option value="<?= $employee->id; ?>"><?= $employee->nip . " - " . $employee->name; ?></option>
                                        <?php
                                            }
                                        }
                                        ?>

                                    </select>
                                    <label for="floatingInput">NIP</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" style="height: 50px;">
                                Nama
                            </div>
                            <div class="col-md-6" style="height: 50px;">
                                : <span id="dnama"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" style="height: 50px;">
                                Jabatan
                            </div>
                            <div class="col-md-6" style="height: 50px;">
                                : <span id="djabatan"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" style="height: 50px;">
                                Jam Masuk
                            </div>
                            <div class="col-md-6" style="height: 50px;">
                                : <span id="djam_masuk"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" style="height: 50px;">
                                Istirahat Mulai
                            </div>
                            <div class="col-md-6" style="height: 50px;">
                                : <span id="distirahat_mulai"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" style="height: 50px;">
                                Istirahat Selesai
                            </div>
                            <div class="col-md-6" style="height: 50px;">
                                : <span id="distirahat_selesai"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6" style="height: 50px;">
                                Jam Pulang
                            </div>
                            <div class="col-md-6" style="height: 50px;">
                                : <span id="djam_pulang"></span>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <button class="btn form-control btn-show-detail btn-warning " data-btn="istirahat">
                            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Absen Istirahat
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn form-control btn-show-detail btn-primary " data-btn="kehadiran">
                            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Absen Kehadiran
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>

<div class="modal detail-modal" tabindex="1">
    <input type="hidden" name="status_type" id="status_type" value="">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title title-secondary"><label class="title-detail-name"></label>PIN</h5>
            </div>
            <div class="modal-body">
                <form class="detail-form" role="form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" class="id_detail" name="id_detail" id="id_detail" />
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="password" class="form-control pin" id="pin" name="pin" placeholder="PIN">
                                <label for="floatingInput">PIN</label>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <div class="d-flex">
                    <button type="button" class="btn btn-hide-detail btn-discard mr-2">Batal</button>
                    <button type="submit" class="btn btn-submit-pin btn-submit-detail">Proses</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>