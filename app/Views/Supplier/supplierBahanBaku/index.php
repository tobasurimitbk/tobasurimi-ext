<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<div class="modal add-modal" id="add_modal">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><label class="title-name"></label> Supplier Bahan Baku</h5>
            </div>
            <div class="modal-body">
                <form class="create-form" role="form" method="POST" enctype="multipart/form-data">
                    <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control name" id="name" name="name" placeholder="Nama">
                                <label for="floatingInput">Nama</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control no_npwp" id="no_npwp" name="no_npwp" placeholder="Nomor NPWP (Opsional)">
                                <label for="floatingInput">Nomor NPWP (Opsional)</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3">
                                <textarea autocomplete="one-time-code" class="form-control address text-area-all" name="address" id="address" placeholder="Alamat (Opsional)"></textarea>
                                <label for="floatingInput">Alamat (Opsional)</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-hide-form btn-hide-parent btn-discard mr-2">Batal</button>
                <button type="submit" class="btn btn-submit-form btn-submit-parent">Simpan</button>
                <button type="button" class="btn btn-discard delete-form delete-btn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<div class="modal harga-modal" id="harga_modal">
    <div class="modal-dialog" style="min-width: 900px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Harga Bahan Baku</h5>
            </div>
            <div class="modal-body">        
                <input autocomplete="one-time-code" type="hidden" class="id_supplier" name="id_supplier" id="id_supplier" />
                <input autocomplete="one-time-code" type="hidden" class="id_supplier_harga" name="id_supplier_harga" id="id_supplier_harga" />
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input disabled autocomplete="one-time-code" type="text" class="form-control name_supplier" id="name_supplier" name="name_supplier" placeholder="Nama Supplier">
                            <label for="floatingInput">Nama Supplier</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-floating mb-3">
                            <textarea disabled autocomplete="one-time-code" class="form-control address_supplier text-area-all" name="address_supplier" id="address_supplier" placeholder="Alamat (Opsional)"></textarea>
                            <label for="floatingInput">Alamat (Opsional)</label>
                        </div>
                    </div>
                </div>
                <form class="harga-form" role="form" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select bahan_baku" name="bahan_baku" id="bahan_baku" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Bahan Baku</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <select class="form-select bagian" name="bagian" id="bagian" aria-label="Floating label select example">
                                    <option value=""></option>
                                </select>
                                <label for="floatingInput">Bagian</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control spesifikasi" id="spesfikasi" name="spesifikasi" placeholder="Spesifikasi">
                                <label for="floatingInput">Spesifikasi</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" onkeyup="formatNumber(this)" type="text" class="form-control harga_umum" id="harga_umum" name="harga_umum" placeholder="Harga Umum">
                                <label for="floatingInput">Harga Umum</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" onkeyup="formatNumber(this)" type="text" class="form-control harga_harian" id="harga_harian" name="harga_harian" placeholder="Harga Harian">
                                <label for="floatingInput">Harga Harian</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" onkeyup="formatNumber(this)" type="text" class="form-control harga_bulanan" id="harga_bulanan" name="harga_bulanan" placeholder="Harga Bulanan">
                                <label for="floatingInput">Harga Bulanan</label>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-hide-form btn-hide-harga btn-discard mr-2">Batal</button>
                    <button type="submit" class="btn btn-submit-form btn-submit-harga">Simpan</button>
                </div>
                <div class="table-responsive mt-3 mb-3">
                    <table class="table-inside table-borderd nowrap table-hover-tobasurimi" width="100%" cellspacing="0" id="tabelKomponenGaji">
                        <thead class="thead-dark">
                            <tr>
                                <th width="10">No</th>
                                <th>Bahan Baku</th>
                                <th>Bagian</th>
                                <th>Spesifikasi</th>
                                <th>Harga Umum</th>
                                <th>Harga Harian</th>
                                <th>Harga Bulanan</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Supplier Bahan Baku</h1>
        <button class="btn btn-show-form btn-add float-right" data-btn="create-modal">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mb-3">
                <div class="col-md-2">
                    <input autocomplete="one-time-code" class="form-control search form-out-search" placeholder="Cari Nama" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th onclick="changeSort('name')" class="sort">Nama</th>
                                <th onclick="changeSort('no_npwp')" class="sort">NPWP</th>
                                <th onclick="changeSort('address')" class="sort">Alamat</th>
                                <th>Set Harga</th>
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
    let sort = "name";
    let sortType = "asc";
    let trigger = true;
    let list_harga = [];

    $('.bahan_baku').select2({
        placeholder: "Pilih Bahan Baku",
        theme: "bootstrap-5",
        dropdownParent: $(".harga-modal .modal-content")
    })

    $('.bagian').select2({
        placeholder: "Pilih Bagian",
        theme: "bootstrap-5",
        dropdownParent: $(".harga-modal .modal-content")
    })

    //CSS SELECT2 FLOATING LABEL
    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.form-select')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.form-select')
        .parent('div')
        .find('label')
        .css('z-index', '1');

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
            url: "<?= base_url("supplier-bahan-baku/all"); ?>",
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
            data: "no_npwp",
            className: "text-center"
        }, {
            data: "address",
            className: "text-center"
        }, {
            data: "id",
            className: "text-center actions",
            searchable: false,
            sortable: false,
            render: function(data, type, row) {
                let id = row?.id;
                    return `
                        <button class="btn btn-success" onclick="setHarga(${id})" style="box-shadow: none !important;">
                            Set Harga
                        </button>
                    `
            }
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

    var validator_detail = $(".harga-form").validate({
        rules: {
            bahan_baku: {
                required: true
            },
            bagian: {
                required: true
            },
            spesifikasi: {
                required: true
            },
            harga_umum: {
                required: true
            },
            harga_harian: {
                required: true
            },
            harga_bulanan: {
                required: true
            }
        },
        messages: {
            bahan_baku: {
                required: "Bahan Baku wajib diisi"
            },
            bagian: {
                required: "Bagian wajib diisi"
            },
            spesifikasi: {
                required: "Spesifikasi wajib diisi"
            },
            harga_umum: {
                required: "Harga Umum wajib diisi"
            },
            harga_harian: {
                required: "Harga Harian wajib diisi"
            },
            harga_bulanan: {
                required: "Harga Bulanan wajib diisi"
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

    $(document).ready(function() {
        var validator = $(".create-form").validate({
            rules: {
                name: {
                    required: true
                },
                no_npwp: {
                    minlength: 15,
                    maxlength: 15,
                }
            },
            messages: {
                name: {
                    required: "Nama wajib diisi"
                },
                no_npwp: {
                    minlength: "Nomor NPWP minimal 15 angka",
                    maxlength: "Nomor NPWP maksimal 15 angka",
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

        $(".no_npwp").mask("000000000000000")

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $(".dataTable_info").addClass("pt-0");

        $(".btn-show-form").click(function() {
            $(".id").val("");
            $(".title-name").text("Tambah");

            validator.resetForm();
            validator.reset();

            $(".create-form")[0].reset()
            $(".delete-form").css('display', 'none');

            $(".add-modal").modal("show");
        })

        $(".btn-hide-parent").click(function() {
            $(".add-modal").modal("hide")
        })

        $(".btn-hide-harga").click(function() {
            $(".harga-modal").modal("hide")
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            $(".create-form")[0].reset()
            $(".delete-form").css('display', '');
            let id = data.id;
            $(".title-name").text("Update");

            $.ajax({
                url: "<?= base_url("supplier/id"); ?>" + "/" + id,
                method: "GET",
                dataType: "json",
                success: function(res) {
                    if (res.status) {
                        $(".id").val(id);
                        $(".name").val(res?.data?.name);
                        $(".address").val(res?.data?.address);
                        $(".no_npwp").val(res?.data?.no_npwp);

                        validator.resetForm();
                        validator.reset();

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
                        url: "<?= base_url("supplier/delete"); ?>",
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
                                title: 'Data Gagal Disimpan, coba Lagi',
                                confirmButtonColor: '#4e73df',
                            })
                            stopLoading()
                        }
                    });
                }
            })
        })

        $(".btn-submit-parent").click(function() {
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

                        $.ajax({
                            url: id ? "<?= base_url("supplier-bahan-baku/update"); ?>" : "<?= base_url("supplier-bahan-baku/save"); ?>",
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
        })

        $(".btn-submit-harga").click(function() {
            if ($(".harga-form").valid()) {
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
                        let data = new FormData(document.querySelector(".harga-form"));
                        let id = $(".id_supplier_harga").val();
                        data.append("id_supplier", $(".id_supplier").val());
                        data.append("id_supplier_harga", $(".id_supplier_harga").val());

                        $.ajax({
                            url: id ? "<?= base_url("supplier-harga/update"); ?>" : "<?= base_url("supplier-harga/save"); ?>",
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
                                $(".harga-form")[0].reset()
                                $(".bahan_baku").val('').change()
                                $(".bagian").val('').change()
                                $(".harga_umum").val(0)
                                $(".harga_harian").val(0)
                                $(".harga_bulanan").val(0)
                                $(".id_supplier_harga").val('')

                                //reset table
                                functionHarga()

                                if (response.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
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
        })
    })

    const setHarga = function(id) {
        validator_detail.resetForm();
        validator_detail.reset();
        $(".harga-form")[0].reset()

        $.ajax({
            url: `<?= base_url("barang/dropdown/type"); ?>`,
            method: "GET",
            data: {
                type: "bahan_baku"
            },
            dataType: "json",
            success: function(res) {
                $(".bahan_baku").empty()

                $(".bahan_baku").append(`<option value=""></option>`)

                res.data.forEach(function(item) {
                    $(".bahan_baku").append(`<option value="${item.id}">${item.barang_name}</option>`)
                })

                $(".bahan_baku").val("").change();
            }
        }) 
            
        $.ajax({
            url: `<?= base_url("warehouse/dropdown"); ?>`,
            method: "GET",
            dataType: "json",
            success: function(res) {
                $(".bagian").empty()

                $(".bagian").append(`<option value=""></option>`)

                res.data.forEach(function(item) {
                    $(".bagian").append(`<option value="${item.id}">${item.warehouse_name}</option>`)
                })

                $(".bagian").val("").change();
            }
        }) 
            
        $.ajax({
            url: "<?= base_url("supplier/id"); ?>" + "/" + id,
            method: "GET",
            dataType: "json",
            success: function(res) {
                if (res.status) {
                    $(".id_supplier").val(id);
                    $(".name_supplier").val(res?.data?.name);
                    $(".address_supplier").val(res?.data?.address);

                    $(".bahan_baku").val('').change()
                    $(".bagian").val('').change()
                    $(".harga_umum").val(0)
                    $(".harga_harian").val(0)
                    $(".harga_bulanan").val(0)

                    $.ajax({
                        url: `<?= base_url("supplier-harga/ajax"); ?>`,
                        method: "GET",
                        data: {
                            id: id
                        },
                        dataType: "json",
                        success: function(result) {
                            console.log(res)
                            let no = 0;
                            $(".body-detail-table").empty()
                            let tag_html = "";

                            result?.data?.map((item) => {
                                no = no + 1;
                                tag_html += "<tr>";
                                tag_html += "<td>";
                                tag_html += no;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += item?.barang_name;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += item?.bagian_name;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += item?.spesifikasi;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += Number(item.harga_umum).toLocaleString();
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += Number(item.harga_harian).toLocaleString();
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += Number(item.harga_bulanan).toLocaleString();
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += `<button onclick="editHarga(${item?.id})" class="btn btn-warning posting-spp">
                                    <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                                </button>`;
                                tag_html += "</td>";
                                tag_html += "<td>";
                                tag_html += `<button class="btn btn-danger" onclick="deleteHarga(${item?.id})">
                                    <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                </button>`;
                                tag_html += "</td>";
                                tag_html += "</tr>";
                            })
                            $(".body-detail-table").append(tag_html)
                            $(".harga-modal").modal("show");
                        }
                    }) 

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: res.message,
                        confirmButtonColor: '#4e73df',
                    })
                }
            }
        })
    }

    const editHarga = function(id) {
         $.ajax({
            url: `<?= base_url("supplier-harga/id/"); ?>` + id,
            method: "GET",
            dataType: "json",
            success: function(result) {
                console.log(result)
                if(result.status === true)
                {
                    $(".id_supplier_harga").val(result?.data?.id);
                    $(".harga_harian").val(Number(result.data.harga_harian).toLocaleString());
                    $(".harga_umum").val(Number(result.data.harga_umum).toLocaleString());
                    $(".harga_bulanan").val(Number(result.data.harga_bulanan).toLocaleString());
                    $(".bahan_baku").val(Number(result.data.bahan_baku_id)).change();
                    $(".bagian").val(Number(result.data.bagian_id)).change();
                    $(".spesifikasi").val(result?.data?.spesifikasi);
                }
                else
                {
                    $(".id_supplier_harga").val('');
                    $(".harga_harian").val(0);
                    $(".harga_umum").val(0);
                    $(".harga_bulanan").val(0);
                    $(".bahan_baku").val('').change();
                    $(".bagian").val('').change();
                    $(".spesifikasi").val('');
                }
            }
        })
    }

    const deleteHarga = function(id) {
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
                    $.ajax({
                        url: "<?= base_url("supplier-harga/delete"); ?>",
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
                                $(".id_supplier_harga").val('');
                                $(".harga_harian").val(0);
                                $(".harga_umum").val(0);
                                $(".harga_bulanan").val(0);
                                $(".bahan_baku").val('').change();
                                $(".bagian").val('').change();
                                $(".spesifikasi").val('');

                                Swal.fire({
                                    icon: 'success',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: response.message,
                                    confirmButtonColor: '#4e73df',
                                })
                            }

                            // reset table
                            functionHarga()
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

    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }

    let functionHarga = function() {
        $.ajax({
            url: `<?= base_url("supplier-harga/ajax"); ?>`,
            method: "GET",
            data: {
                id: $(".id_supplier").val()
            },
            dataType: "json",
            success: function(result) {
                let no = 0;
                $(".body-detail-table").empty()
                let tag_html = "";

                result?.data?.map((item) => {
                    no = no + 1;
                    tag_html += "<tr>";
                    tag_html += "<td>";
                    tag_html += no;
                    tag_html += "</td>";
                    tag_html += "<td>";
                    tag_html += item?.barang_name;
                    tag_html += "</td>";
                    tag_html += "<td>";
                    tag_html += item?.bagian_name;
                    tag_html += "</td>";
                    tag_html += "<td>";
                    tag_html += item?.spesifikasi;
                    tag_html += "</td>";
                    tag_html += "<td>";
                    tag_html += Number(item.harga_umum).toLocaleString();
                    tag_html += "</td>";
                    tag_html += "<td>";
                    tag_html += Number(item.harga_harian).toLocaleString();
                    tag_html += "</td>";
                    tag_html += "<td>";
                    tag_html += Number(item.harga_bulanan).toLocaleString();
                    tag_html += "</td>";
                    tag_html += "<td>";
                    tag_html += `<button onclick="editHarga(${item?.id})" class="btn btn-warning posting-spp">
                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                    </button>`;
                    tag_html += "</td>";
                    tag_html += "<td>";
                    tag_html += `<button class="btn btn-danger" onclick="deleteHarga(${item?.id})">
                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                    </button>`;
                    tag_html += "</td>";
                    tag_html += "</tr>";
                })
                $(".body-detail-table").append(tag_html)
            }
        }) 
    }
</script>


<?= $this->endSection(); ?>