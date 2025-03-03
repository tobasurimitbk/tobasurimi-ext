<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal-nobukti" id="add_modal_nobukti" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title modal-title-nobukti"><label class="title-name-nobukti"></label> Kategori Akun</h5>
            </div>
            <div class="modal-body">
                <form class="create-form-nobukti" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id_nobukti" name="id_nobukti" id="id_nobukti" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control nama_tipe_transaksi" id="nama_tipe_transaksi" name="nama_tipe_transaksi" placeholder="Nama Tipe Transaksi">
                                <label for="floatingInput">Nama Tipe Transaksi</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control no_bukti" id="no_bukti" name="no_bukti" placeholder="Kode Transaksi/ No Bukti">
                                <label for="floatingInput">Kode Transaksi/ No Bukti</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form-nobukti btn-discard mr-2">Kembali</button>
                <button type="submit" class="btn btn-submit-form btn-submit-form-nobukti">Simpan</button>
                <button type="button" class="btn btn-discard delete-btn delete-btn-nobukti">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>No Bukti Accounting</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="nobukti" role="tabpanel" aria-labelledby="nobukti-tab">
                    <div class="collapse-nobukti-list show" id="collapseKategoriList">
                        <div class="d-flex float-right mb-3">
                            <input autocomplete="one-time-code" class="form-control search search-nobukti form-out-search mr-3" placeholder="Search" />
                            <button class="btn btn-show-form btn-add float-right btn-show-form-nobukti" data-btn="create-modal">
                                <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered nowrap table-hover-tobasurimi nobuktiDataTable" id="nobuktiDataTable" width="100%" cellspacing="0">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No.</th>
                                        <th onclick="changeSortKategori('type')" class="sort">Type Transaksi</th>
                                        <th onclick="changeSortKategori('nobukti')" class="sort">No Bukti</th>
                                    </tr>
                                </thead>
                                <tbody class="body-table" id="body-table" style="cursor: pointer;">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';
    let sortKategori = "value";
    let sortTypeKategori = "asc";

    const nobuktiTable = $('.nobuktiDataTable').DataTable({

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
            url: "<?= base_url("no-bukti/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search-nobukti").val();
                data.sort = sortKategori;
                data.sortType = sortTypeKategori;
            }
        },
        // scrollX: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.nobuktiDataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        //responsive: true,
        display: "stripe",
        searching: false,
        columns: [{
                data: "no",
                className: "text-center",
                sortable: false,
                width: "5%"
            }, {
                data: "value",
                className: "text-center"
            },
            {
                data: "description",
                className: "text-center"
            }
        ],
        columnDefs: [{
            defaultContent: "-",
            targets: "_all"
        }],
        language: {
            emptyTable: "Tidak Ada Data",
            lengthMenu: "Show _MENU_ entries",
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        }
    });

    $(document).ready(function() {

        var validator_nobukti = $(".create-form-nobukti").validate({
            rules: {
                nama_tipe_transaksi: {
                    required: true
                },
                no_bukti: {
                    required: true
                }
            },
            messages: {
                nama_tipe_transaksi: {
                    required: "Nama Tipe Transaksi wajib diisi"
                },
                no_bukti: {
                    required: "Kode Transaksi/ No Bukti wajib diisi"
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

        $(".dataTable_info").addClass("pt-0");

        $('#nobuktiDataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = nobuktiTable.row(this).data();
            $(".create-form-nobukti")[0].reset()
            $(".delete-btn-nobukti").css('display', '');
            let id = data.id;
            $(".title-name-nobukti").text("Update");

            $.ajax({
                url: "<?= base_url("no-bukti/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".id_nobukti").val(id);
                        $(".nama_tipe_transaksi").val(res.data.value);
                        $(".no_bukti").val(res.data.description);

                        validator_nobukti.resetForm();
                        validator_nobukti.reset();
                        $(".add-modal-nobukti").modal("show")

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        })
                    }
                }
            })
        })

        $(".search-nobukti").keyup(function() {
            nobuktiTable.ajax.reload();
        })

        $(".btn-show-form-nobukti").click(function() {
            $(".id_nobukti").val("");

            $(".title-name-nobukti").text("Tambah");

            validator_nobukti.resetForm();
            validator_nobukti.reset();
            $(".create-form-nobukti")[0].reset()
            $(".delete-btn-nobukti").css('display', 'none');
            $(".add-modal-nobukti").modal("show");
        })

        $(".btn-hide-form-nobukti").click(function() {
            $(".add-modal-nobukti").modal("hide")
        })

        $(".btn-submit-form-nobukti").click(function() {
            if ($(".create-form-nobukti").valid()) {
                Swal.fire({
                    icon: 'question',
                    title: 'Simpan Data?',
                    confirmButtonColor: '#4e73df',
                    cancelButtonColor: '#d33',
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: 'Simpan',
                    cancelButtonText: 'Kembali',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrf = $(`[name="${csrfToken}"]`);
                        setLoading()
                        let data = new FormData(document.querySelector(".create-form-nobukti"));

                        let id = $(".id_nobukti").val();
                        // UPDATE
                        if (id) {
                            $.ajax({
                                url: "<?= base_url("no-bukti/update"); ?>",
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
                                        stopLoading()
                                        Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                $(".add-modal-nobukti").modal("hide")
                                                $(".create-form-nobukti")[0].reset()

                                                nobuktiTable.ajax.reload()
                                            })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        stopLoading()
                                    }
                                },
                                onError: function(response) {
                                    csrf.val(response.token);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    })
                                    stopLoading()
                                }
                            });
                        }
                        // CREATE
                        else {
                            $.ajax({
                                url: "<?= base_url("no-bukti/save"); ?>",
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
                                        stopLoading()
                                        Swal.fire({
                                                icon: 'success',
                                                title: response.message,
                                                confirmButtonColor: '#4e73df',
                                            })
                                            .then(() => {
                                                $(".add-modal-nobukti").modal("hide")
                                                $(".create-form-nobukti")[0].reset()

                                                nobuktiTable.ajax.reload()
                                            })
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: response.message,
                                            confirmButtonColor: '#4e73df',
                                        })
                                        stopLoading()
                                    }
                                },
                                onError: function(response) {
                                    csrf.val(response.token);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Data Gagal Disimpan, coba Lagi',
                                        confirmButtonColor: '#4e73df',
                                    })
                                    stopLoading()
                                }
                            });
                        }
                    }
                })
            }
        })

        $(".delete-btn-nobukti").click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Hapus Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Kembali',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let id = $(".id_nobukti").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("no-bukti/delete"); ?>",
                        data: {
                            id: id
                        },
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                        },
                        method: "POST",
                        dataType: "json",
                        success: function(response) {
                            csrf.val(response.token);
                            if (response.status) {
                                stopLoading()
                                Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                    .then(() => {
                                        $(".add-modal-nobukti").modal("hide")
                                        $(".create-form-nobukti")[0].reset()

                                        nobuktiTable.ajax.reload()
                                    })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                                stopLoading()
                            }
                        },
                        onError: function(response) {
                            csrf.val(response.token);
                            Swal.fire({
                                icon: 'error',
                                title: 'Data Gagal Disimpan, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            })
                            stopLoading()
                        }
                    });
                }
            })
        })
    })

    const changeSortKategori = function(val) {
        if (sortKategori !== val) {
            sortTypeKategori = "asc";
            sortKategori = val;
        } else {
            sortTypeKategori = sortTypeKategori === "asc" ? "desc" : "asc";
        }
    }
</script>

<?= $this->endSection(); ?>