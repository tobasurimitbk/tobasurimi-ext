<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" id="add_modal">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Account Module</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <input autocomplete="one-time-code" type="text" class="form-control name" id="name" name="name" placeholder="Nama">
                                        <label for="floatingInput">Nama</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select module" name="module" id="module">
                                            <option value="">Pilih Module</option>
                                            <option value="pembelian">Pembelian</option>
                                            <option value="penjualan">Penjualan</option>
                                        </select>
                                        <label for="floatingInput">Module</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select tipe" name="tipe" id="tipe">
                                            <option value="">Pilih Tipe</option>
                                            <option value="BAHAN BAKU">BAHAN BAKU</option>
                                            <option value="BAHAN PENOLONG">BAHAN PENOLONG</option>
                                        </select>
                                        <label for="floatingInput">Tipe Barang Disediakan</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3" style="height: 50px;">
                                        <select class="form-select kategori" name="kategori" id="kategori">
                                            <option value="">Pilih Kategori</option>
                                            <option value="LOKAL">LOKAL</option>
                                            <option value="IMPORT">IMPORT</option>
                                            <option value="EKSPORT">EKSPORT</option>
                                        </select>
                                        <label for="floatingInput">Kategori Supplier</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
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
                                        <label for="floatingInput">AP</label>
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
                                        <label for="floatingInput">AR</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-discard mr-2">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-parent">Simpan</button>
                <button type="button" class="btn btn-discard delete-form delete-btn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Account Module</h1>
        <button class="btn btn-show-form btn-add float-right" data-btn="create-modal">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </button>
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
                                <th onclick="changeSort('name')" class="sort">Nama</th>
                                <th onclick="changeSort('ap')" class="sort">AP</th>
                                <th onclick="changeSort('ar')" class="sort">AR</th>
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
    let sort = "kode";
    let sortType = "desc";
    let trigger = true;

    let list_address = [];
    let list_delete = [];
    var row = 0;

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
            url: "<?= base_url("account-module/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                data.sort = sort;
                data.sortType = sortType;
            }
        },
        // scrollX: true,
        "initComplete": function(settings, json) {
            $('.dataTables_length').empty();
            $('.dataTables_length').html("<div><label class='text-center ml-2 mt-2'>Show <b class='entries-label'>25</b> Entries</label></div>");
            $('.dataTable').wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
        },
        //responsive: true,
        display: "stripe",
        searching: false,
        columns: [{
            data: "no",
            className: "text-center",
            sortable: false
        }, {
            data: "name",
            className: "text-center"
        }, {
            data: "ap_id",
            className: "text-center"
        }, {
            data: "ar_id",
            className: "text-center"
        }],
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

    var validator_detail = $(".detail-form").validate({
        rules: {
            detail_address: {
                required: true
            },
            province_id: {
                required: true
            },
            city_id: {
                required: true
            }
        },
        messages: {
            detail_address: {
                required: "Address wajib diisi"
            },
            province_id: {
                required: "Province wajib diisi"
            },
            city_id: {
                required: "City wajib diisi"
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

    $(document).ready(function() {
        const select2Prop = {
            dropdownParent: $("#add_modal"),
            ajax: {
                delay: 300,
                url: `<?= base_url("sub-account/dropdown"); ?>`,
                dataType: 'json',
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    }
                }
            }
        };

        // PROVINCE
        $('.province_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content")
        });

        //CSS SELECT2 FLOATING LABEL
        $(".province_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".province_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px')

        $(".province_id")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // PROVINCE PARENT
        $('.akun_ap_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $(".akun_ap_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".akun_ap_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px')

        $(".akun_ap_id")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // CITY
        $('.city_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".detail-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $(".city_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $(".city_id")
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px')

        $(".city_id")
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // CITY PARENT
        $('.akun_ar_id').select2({
            placeholder: "",
            theme: "bootstrap-5",
            dropdownParent: $(".add-modal .modal-content")
        })

        //CSS SELECT2 FLOATING LABEL
        $('.akun_ar_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.akun_ar_id')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px')

        $('.akun_ar_id')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        var validator = $(".create-form").validate({
            rules: {
                kode: {
                    required: true
                },
                name: {
                    required: true
                },
                akun_ap_id: {
                    // required: true
                },
                akun_ar_id: {
                    // required: true
                },
            },
            messages: {
                kode: {
                    required: "Kode wajib diisi"
                },
                name: {
                    required: "Nama wajib diisi"
                },
                akun_ap_id: {
                    required: "Provinsi wajib diisi"
                },
                akun_ar_id: {
                    required: "Kota wajib diisi"
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

        $(".phone").mask("0000000000000")

        $(".postal_code").mask("00000")

        $(".no_npwp").mask("000000000000000")

        // $(".no_rekening").mask("000000000000000")

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".dataTable_info").addClass("pt-0");

        $(".btn-show-detail").click(function() {
            $(".delete-detail").css('display', 'none');

            $(".title-detail-name").text("Tambah")

            validator_detail.resetForm();
            validator_detail.reset();

            $(".detail-modal").modal("show")
        })

        $(".btn-show-form").click(function() {
            $('.input-generate').show();

            $(".id").val("");
            $(".title-name").text("Tambah");

            $(".akun_ap_id").val('').change()
            $(".akun_ar_id").val('').change()

            $(".kode").attr("readonly", false);
            $(".tipe").attr('disabled', true)
            $(".kategori").attr('disabled', true)

            $(".body-detail-table").empty()

            row = 0;

            list_address = [];

            validator.resetForm();
            validator.reset();

            $(".create-form")[0].reset()
            $(".delete-form").css('display', 'none');
            $(".body-detail-table").empty()

            // $('.ap_id').select2(select2Prop);
            // $('.ar_id').select2(select2Prop);

            $(".add-modal").modal("show");
        })

        $(".btn-hide-detail").click(function() {
            $(".detail-modal").modal("hide")
        })

        $(".btn-hide-form").click(function() {
            $(".add-modal").modal("hide")
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            $(".create-form")[0].reset()
            $(".delete-form").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");

            $(".kode").attr("readonly", true);

            $.ajax({
                url: "<?= base_url("account-module/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $('.input-generate').hide()

                        $(".id").val(id);
                        $(".name").val(res?.data?.name);
                        $(".akun_ap_id").val(res?.data?.ap_id).change();
                        $(".akun_ar_id").val(res?.data?.ar_id).change();
                        $(".tipe").val(res?.data?.type).change();
                        $(".kategori").val(res?.data?.kategori).change();
                        $(".module").val(res?.data?.module).change();

                        row = res?.data?.list_address.length;

                        list_address = [];

                        let tag_html = "";

                        $(".body-detail-table").empty()

                        res?.data?.list_address.map((item, index) => {
                            list_address.push({
                                id: item.id,
                            })

                            tag_html += `<tr>`;
                            tag_html += `<td class="edit-table-detail" data-id="${item.id}" data-row="${index + 1}" data-address="${item.address}" data-province="${item.province_id}" data-city="${item.city_id}" data-postalcode="${item.postal_code}">`;
                            tag_html += index + 1;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-id="${item.id}" data-row="${index + 1}" data-address="${item.address}" data-province="${item.province_id}" data-city="${item.city_id}" data-postalcode="${item.postal_code}">`;
                            tag_html += item.address;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-id="${item.id}" data-row="${index + 1}" data-address="${item.address}" data-province="${item.province_id}" data-city="${item.city_id}" data-postalcode="${item.postal_code}">`;
                            tag_html += item.city_name;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-id="${item.id}" data-row="${index + 1}" data-address="${item.address}" data-province="${item.province_id}" data-city="${item.city_id}" data-postalcode="${item.postal_code}">`;
                            tag_html += item.province_name;
                            tag_html += "</td>";
                            tag_html += `<td class="edit-table-detail" data-id="${item.id}" data-row="${index + 1}" data-address="${item.address}" data-province="${item.province_id}" data-city="${item.city_id}" data-postalcode="${item.postal_code}">`;
                            tag_html += item.postal_code;
                            tag_html += "</td>";
                            tag_html += "<td class='actions'>";
                            if (item.main_address == 1) {
                                tag_html += `<input autocomplete="one-time-code" type="radio" checked id="main" name="main" value="${index + 1}">`;
                            } else {
                                tag_html += `<input autocomplete="one-time-code" type="radio" id="main" name="main" value="${index + 1}">`;
                            }
                            tag_html += "</td>";
                            tag_html += "</tr>";
                        })

                        $(".body-detail-table").append(tag_html)

                        validator.resetForm();
                        validator.reset();
                        list_delete = [];

                        $(".add-modal").modal("show");

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

        // delete
        $(".delete-form").click(function() {
            Swal.fire({
                icon: 'question',
                title: 'Hapus Data?',
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#d33',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    const csrf = $(`[name="${csrfToken}"]`);
                    let id = $(".id").val();
                    setLoading()
                    $.ajax({
                        url: "<?= base_url("account-module/delete"); ?>",
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
                                        table.ajax.reload()
                                        $(".add-modal").modal("hide")
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
                                title: 'Data Gagal Dihapus, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            })
                            stopLoading()
                        }
                    });
                }
            })
        })

        $(".btn-submit-parent").click(function() {
            $(".detail-modal").modal("hide")
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

                        let update_list_address = [];

                        let id = $(".id").val();

                        $.ajax({
                            url: id ? "<?= base_url("account-module/update"); ?>" : "<?= base_url("account-module/save"); ?>",
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
                                            $(".add-modal").modal("hide")
                                            table.ajax.reload()
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
            }
            // }
        })
    })

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    const changeStatus = function() {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if (value) {
            $(".kode").attr("readonly", true);
            $(".kode").val("AUTO GENERATE");
        } else {
            $(".kode").attr("readonly", false);
            $(".kode").val("");
        }
    }

    // Module
    $('.module').select2({
        placeholder: "",
        theme: "bootstrap-5",
        dropdownParent: $(".add-modal .modal-content")
    });

    $(".module")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $(".module")
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px')

    $(".module")
        .parent('div')
        .find('label')
        .css('z-index', '1');

    // DISABLED PROVINSI DAN KABUPATEN JIKA YANG DIPILIH BUKAN INDONESIA
    $('.module').on('change', function() {
        var codeModule = $(this).val();
        if (codeModule == "pembelian") {
            $(".tipe").attr('disabled', false)
            $(".kategori").attr('disabled', false)
            // $(".kategori").empty()
            // $(".kategori").append(`<option value="">Pilih Kategori</option>`)
            // $(".kategori").append(`<option value="LOKAL">LOKAL</option>`)
            // $(".kategori").append(`<option value="IMPORT">IMPORT</option>`)
        } else {
            $(".tipe").attr('disabled', false)
            $(".kategori").attr('disabled', false)
            // $(".kategori").empty()
            // $(".kategori").append(`<option value="">Pilih Kategori</option>`)
            // $(".kategori").append(`<option value="LOKAL">LOKAL</option>`)
            // $(".kategori").append(`<option value="EKSPORT">EKSPORT</option>`)
        }
    });

    // RESTART SELECT2 KETIKA KLIK TAMBAH BUTTON
    $('.btn-add').click(function() {
        $('.country_code').val("").change();
        $('.ap_id').val(null).trigger("change");
        $('.ar_id').val(null).trigger("change");
        $('.akun_ap_id').attr('disabled', false);
        $('.akun_ar_id').attr('disabled', false);
    });
</script>


<?= $this->endSection(); ?>