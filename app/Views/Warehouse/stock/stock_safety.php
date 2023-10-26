<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>
<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label></h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control" id="tipeBarang" name="tipeBarang" placeholder="Tipe Barang" disabled>
                                <label for="floatingInput">Tipe Barang</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="number" class="form-control" id="safetyNumber" name="safetyNumber" placeholder="Angka Safety Stock" pattern="[0-9]*" title="Angka harus diawali dengan angka 0-9">
                                <label for="floatingInput">Angka Safety Stok</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input name="reStockNumber" autocomplete="one-time-code" type="number" id="reStockNumber" class="form-control target input-picker" value="" pattern="[0-9]*" title="Angka harus diawali dengan angka 0-9">
                                <label for="reStockNumber" id="reStockNumber">Angka Harus Restok</label>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Batal</button>
                <button type="submit" class="btn btn-submit-form">Simpan</button>
            </div>
        </div>
    </div>
</div>
<section class="section">
    <div class="section-header">
        <h1>Stok Safety</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mb-3">
                <div class="col-md-2">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Tipe Barang</th>
                                <th>Safety</th>
                                <th>Harus Restok</th>
                            </tr>
                        </thead>
                        <tbody class="body-table" id="body-table" style="cursor: pointer;">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sort = "parent_barang";
    let sortType = "asc";

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [1, 'asc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("stock-safety/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                sortable: false,
                width: "5%"
            }, {
                data: "tipeBarang",
                className: "text-center",
            },
            {
                data: "safetyNumber",
                className: "text-center",
                width: "10%"
            },
            {
                data: "reStockNumber",
                className: "text-center",
                width: "10%"
            }
        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Terjadi Kesalahan",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $(".search").keyup(function() {
        table.ajax.reload();
    });

    $('.btn-hide-form').click(function() {
        $(".add-modal").modal("hide");
    });

    var validator = $(".create-form").validate({
        rules: {
            safetyNumber: {
                required: true,
                number: true
            },
            reStockNumber: {
                required: true,
                number: true
            }
        },
        messages: {
            safetyNumber: {
                required: "Angka Safety Stok wajib diisi",
                number: "Masukkan angka valid"
            },
            reStockNumber: {
                required: "Angka harus Restok wajib diisi",
                number: "Masukkan angka valid"
            }
        },
        errorElement: 'span',
        errorClass: 'text-danger',
        errorPlacement: function(error, element) {
            var elem = $(element);
            if (elem.hasClass("select2-hidden-accessible")) {
                element = $("#select2-" + elem.attr("id") + "-container").parent();
                error.insertAfter(element);
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
            $(element).addClass('select-class');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
            $(element).removeClass('select-class');
        },
    });


    $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
        const data = table.row(this).data();
        $(".create-form")[0].reset()
        $(".delete-btn").show();
        $(".title-name").text("Update Stock Safety " + data?.tipeBarang);
        let id = data.id;

        validator.resetForm();
        validator.reset();

        $.ajax({
            url: "<?= base_url("stock-safety/id"); ?>" + "/" + id,
            method: "GET",
            dataType: "json",
            success: function(res) {
                if (res.status) {
                    $(".id").val(id);
                    $('#tipeBarang').val(res?.data?.tipe_barang);
                    $("#safetyNumber").val(res?.data?.safety_number);
                    $("#reStockNumber").val(res?.data?.re_stock_number);
                    $(".add-modal").modal("show")
                }
            }
        })
    });

    $(".btn-submit-form").click(function() {

        if ($(".create-form").valid()) {
            Swal.fire({
                icon: 'question',
                title: 'Simpan Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let data = new FormData(document.querySelector(".create-form"));
                    let id = $(".id").val();
                    if (id) {
                        $.ajax({
                            url: "<?= base_url("stock-safety/update"); ?>",
                            data: data,
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            },
                            method: "POST",
                            dataType: "json",
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                csrf.val(response.token);
                                if (response.status) {
                                    Swal.fire({
                                            icon: 'success',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        .then(() => {
                                            table.ajax.reload()
                                            $(".add-modal").modal("hide")
                                        })
                                }
                            },
                        });
                    } else {
                        alert("Terjadi kesalahan");
                    }
                }
            })
        }
    });
</script>

<?= $this->endSection(); ?>