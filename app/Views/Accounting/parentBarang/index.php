<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<section class="section">
    <div class="section-header">
        <h1>Akun Barang</h1>
    </div>
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link <?= $type == "" || $type == "bahan_baku" ? "active" : "" ?> " href="<?= base_url('tipe-barang') ?>">Bahan Baku</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $type == "bahan_penolong" ? "active" : "" ?>" href="<?= base_url('tipe-barang?type=bahan_penolong') ?>">Bahan Penolong</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $type == "bahan_jadi" ? "active" : "" ?>" href="<?= base_url('tipe-barang?type=bahan_jadi') ?>">Bahan Jadi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $type == "bahan_scrap" ? "active" : "" ?>" href="<?= base_url('tipe-barang?type=bahan_scrap') ?>">Bahan Scrap</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $type == "bahan_modal" ? "active" : "" ?>" href="<?= base_url('tipe-barang?type=bahan_modal') ?>">Barang Modal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $type == "bahan_setengah_jadi" ? "active" : "" ?>" href="<?= base_url('tipe-barang?type=bahan_setengah_jadi') ?>">Bahan Setengah Jadi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $type == "kemasan" ? "active" : "" ?>" href="<?= base_url('tipe-barang?type=kemasan') ?>">Kemasan</a>
                </li>
            </ul>
            <div class="row justify-content-end mt-4">
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_divisi" name="filter_divisi" id="filter_divisi">
                            <option value="" data-code=""></option>
                            <?php foreach ($divisi as $d) : ?>
                                <option value="<?= $d['id'] ?>"><?= strtoupper($d['divisi']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="floatingInput">Pilih Departemen</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <select class="form-select filter_coa" name="filter_coa" id="filter_coa">
                            <option value="" data-code=""></option>
                            <option value="belum" data-code="" selected>BELUM PUNYA COA</option>
                            <option value="sudah" data-code="">SUDAH PUNYA COA</option>
                        </select>
                        <label for="floatingInput">Filter Akun</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Nama Barang" value="" style="height: 50px;" />
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable dataTable-barang" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark text-center">
                            <tr>
                                <th>No.</th>
                                <th>Barang</th>
                                <th>Departemen</th>
                                <?php if ($type == "bahan_jadi" || $type == "bahan_setengah_jadi") : ?>
                                    <th>Akun Persediaan</th>
                                <?php else : ?>
                                    <th>Akun Pembelian</th>
                                <?php endif; ?>
                                <th>Akun Penjualan</th>
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

<div class="modal add-modal" tabindex="-1">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label></h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data" onSubmit="return false">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <input type="hidden" name="divisi_id" class="divisi_id" id="divisi_id">
                    <input type="hidden" name="barang_id" class="barang_id" id="barang_id">
                    <input type="hidden" name="type" id="type" value="<?= $type ?>">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input disabled autocomplete="one-time-code" type="text" class="form-control" placeholder="Nama Barang" id="parentName" name="parentName">
                                <label for="floatingInput">Nama Barang</label>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input disabled autocomplete="one-time-code" type="text" class="form-control divisis_name" placeholder="Departemen" id="divisi_name" name="divisi_name">
                                <label for="floatingInput">Departemen</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select akun_ap_id" name="akun_ap_id" id="akun_ap_id">
                                    <option value=""></option>
                                    <?php
                                    if (!empty($subAkuns)) {
                                        foreach ($subAkuns as $sub) {
                                    ?>
                                            <option value="<?= $sub->id; ?>"><?= $sub->no_sub; ?> <?= $sub->nama_sub; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <?php if ($type == "bahan_jadi" || $type == "bahan_setengah_jadi") : ?>
                                    <label for="floatingInput">Akun Persediaan</label>
                                <?php else : ?>
                                    <label for="floatingInput">Akun Pembelian</label>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select akun_ar_id" name="akun_ar_id" id="akun_ar_id">
                                    <option value="" data-code=""></option>
                                    <?php
                                    if (!empty($subAkuns)) {
                                        foreach ($subAkuns as $sub_ar) {
                                    ?>
                                            <option value="<?= $sub_ar->id; ?>"><?= $sub_ar->no_sub; ?> <?= $sub_ar->nama_sub; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Akun Penjualan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select akun_pemakaian_id" name="akun_pemakaian_id" id="akun_pemakaian_id">
                                    <option value="" data-code=""></option>
                                    <?php
                                    if (!empty($subAkuns)) {
                                        foreach ($subAkuns as $sub_ar) {
                                    ?>
                                            <option value="<?= $sub_ar->id; ?>"><?= $sub_ar->no_sub; ?> <?= $sub_ar->nama_sub; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Akun Pemakaian</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select kategori" name="kategori" id="kategori">
                                    <option value="" data-code=""></option>
                                    <?php
                                    if (!empty($kategoriBarangAkun)) {
                                        foreach ($kategoriBarangAkun as $kategoriBarang) {
                                    ?>
                                            <option value="<?= $kategoriBarang->id; ?>"><?= $kategoriBarang->description; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <label for="floatingInput">Kategori Barang</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-submit-form">Simpan</button>
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Kembali</button>
            </div>
        </div>
    </div>
</div>

<script>
    let sort = "barang_master.id";
    let sortType = "ASC";

    $(document).ready(function() {
        const csrfToken = '<?= csrf_token() ?>';
        const table = $('.dataTable-barang').DataTable({
            dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
            processing: true,
            serverSide: true,
            ordering: false,
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
                url: "<?= base_url("tipe-barang/all"); ?>",
                dataSrc: "data",
                data: function(data) {
                    data.search = $(".search").val();
                    data.sort = sort;
                    data.sortType = sortType;
                    data.parent_type = "<?= $type ?>";
                    data.filter_coa = $(".filter_coa").val();
                    data.filter_divisi = $(".filter_divisi").val();
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
                data: "parent_name",
                className: "text-left",
                sortable: false,
            }, {
                data: "divisi",
                className: "text-left",
                sortable: false,
            }, {
                data: "ap_no",
                className: "text-center",
                sortable: false,
            }, {
                data: "ar_no",
                className: "text-center",
                sortable: false,
            }, ],
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

        $(".search").keyup(function() {
            table.ajax.reload();
        });
        $(".filter_coa").change(function() {
            table.ajax.reload();
        });
        $(".filter_divisi").change(function() {
            table.ajax.reload();
        });
        // hide modal
        $('.btn-discard').click(function() {
            $('.add-modal').modal('hide');
        });
        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            let csrf = $(`[name="${csrfToken}"]`);
            let id = data.id;
            let divisi_id = data.divisi_id;
            let divisi = data.divisi;
            let parentName = data.parent_name;

            let formData = new FormData();
            $('#parentName').val(null);
            formData.append("id", id);
            formData.append("divisi_id", divisi_id);

            $('.title-name').text("Update Akun Barang");
            $('.delete-btn').show();
            $.ajax({
                url: "<?= base_url("tipe-barang/get"); ?>",
                data: formData,
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                },
                method: "POST",
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(res) {
                    console.log(res);
                    csrf.val();
                    if (res.status) {
                        if (res.data != null) {
                            $("#barang_id").val(res.data.barang_master_id).change();
                            $("#akun_ap_id").val(res.data.ap_id).change();
                            $("#akun_ar_id").val(res.data.ar_id).change();
                            $("#akun_pemakaian_id").val(res.data.pemakaian_id).change();
                            $("#kategori").val(res.data.kategori_id).change();
                        } else {
                            $("#barang_id").val(null).change();
                            $("#akun_ap_id").val(null).change();
                            $("#akun_ar_id").val(null).change();
                            $("#akun_pemakaian_id").val(null).change();
                            $("#kategori").val(null).change();
                        }
                        $("#parentName").val(parentName);
                        $('#divisi_id').val(divisi_id)
                        $('#divisi_name').val(divisi);
                        $('#id').val(id);

                        $('.add-modal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: res.message,
                            confirmButtonColor: '#4e73df',
                        });
                    }
                }
            })
        });
        // init validation
        var validator = $(".create-form").validate({
            rules: {
                parentName: {
                    required: true
                },
                akun_ap_id: {
                    required: true
                },
                akun_ar_id: {
                    required: true
                },
                akun_pemakaian_id: {
                    required: true
                },
            },
            messages: {
                parentName: {
                    required: "Nama Barang Wajib Diisi"
                },
                akun_ap_id: {
                    required: "Akun Pembelian Wajib Diisi"
                },
                akun_ar_id: {
                    required: "Akun Penjualan Wajib Diisi"
                },
                akun_pemakaian_id: {
                    required: "Akun Pemakaian Wajib Diisi"
                },
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
        // action save or update
        $('.btn-submit-form').click(function(e) {
            e.preventDefault();
            if ($(".create-form").valid()) {
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
                        let id = $('input[name="id"]').val();
                        let divisi_id = $('#divisi_id').val();
                        let csrf = $(`[name="${csrfToken}"]`);
                        let data = new FormData(document.querySelector(".create-form"));
                        data.append("divisi_id", divisi_id);
                        $.ajax({
                            url: "<?= base_url("tipe-barang/save"); ?>",
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
                                            table.ajax.reload();
                                            $('#parentName').val(null);
                                            $(".add-modal").modal("hide")
                                        })
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    }).then(() => {
                                        $('#parentName').val(null);
                                        $(".add-modal").modal("hide")
                                    });
                                }
                            },
                            onError: function(response) {
                                csrf.val(response.token);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Data Gagal Disimpan, coba Lagi',
                                    confirmButtonColor: '#4e73df',
                                }).then(() => {
                                    $(".add-modal").modal("hide")
                                });
                            }
                        });
                    }
                })

            }
        });
    });
    // sort
    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    // Akun AR
    $('.filter_divisi').select2({
        placeholder: "Filter Departemen",
        theme: "bootstrap-5",
        allowClear: true,
    })

    // Akun AR
    $('.filter_coa').select2({
        placeholder: "Filter Akun",
        theme: "bootstrap-5",
    })

    //CSS SELECT2 FLOATING LABEL
    $('.filter_coa, .filter_divisi')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.filter_coa,.filter_divisi')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.filter_coa,.filter_divisi')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    //CSS SELECT2 FLOATING LABEL
    $('.akun_ar_id, .akun_ap_id, .kategori, .akun_pemakaian_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.akun_ar_id, .akun_ap_id, .kategori, .akun_pemakaian_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.akun_ar_id, .akun_ap_id, .kategori, .akun_pemakaian_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    // Akun AR
    $('.akun_ar_id').select2({
        placeholder: "",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    }).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    })

    $('.akun_pemakaian_id').select2({
        placeholder: "",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    }).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    })

    // Akun AR
    $('.kategori').select2({
        placeholder: "",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    }).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    })

    // Akun AP
    $('.akun_ap_id').select2({
        placeholder: "",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    }).on("select2:open", () => {
        document.querySelector(".select2-container--open .select2-search__field").focus()
    })

    // $('.akun_ap_id').on("select2:open", () => {
    //     document.querySelector(".select2-container--open .select2-search__field").focus()
    // })
</script>

<?= $this->endSection(); ?>