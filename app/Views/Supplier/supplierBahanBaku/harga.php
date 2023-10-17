<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1 class="title-name">Set Harga Barang</h1>
        <div class="col-button-tambah-spp">
            <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("supplier-bahan-baku"); ?>">
                Batal
            </a>
            <?php if(!empty($dataSupplier)){ ?>
            <button class="btn btn-show-form btn-save float-right btn-submit-harga">
                Simpan
            </button>
            <?php } ?>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <?= csrf_field() ?>
            <table width="100%" class="mb-3">
                <tbody>
                    <tr style="color: black;">
                        <td width="150px">Nama Supplier</td>
                        <td width="5px">:</td>
                        <td><?= empty($dataSupplier) ? "" : $dataSupplier->name; ?></td>
                    </tr>
                    <tr style="color: black; height: 20px;">
                        <td colspan="3"></td>
                    </tr>
                    <tr style="color: black;">
                        <td width="150px">Alamat</td>
                        <td width="25px">:</td>
                        <td><?= empty($dataSupplier) ? "" : $dataSupplier->address; ?></td>
                    </tr>
                </tbody>
            </table>
            <input autocomplete="one-time-code" value="<?= empty($dataSupplier) ? "" : $dataSupplier->id; ?>" type="hidden" class="id_supplier" name="id_supplier" id="id_supplier" />
            <input autocomplete="one-time-code" type="hidden" class="id_supplier_harga" name="id_supplier_harga" id="id_supplier_harga" />
            <form class="harga-form" role="form" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select bahan_baku" name="bahan_baku" id="bahan_baku" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataBarang as $b) : ?>
                                    <option value="<?= $b["id"]; ?>">
                                        <?= $b["barang_name"]; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Bahan Baku</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <select class="form-select bagian" name="bagian" id="bagian" aria-label="Floating label select example">
                                <option value=""></option>
                                <?php foreach ($dataDivisi as $d) : ?>
                                    <option value="<?= $d["id"]; ?>">
                                        <?= $d["divisi"]; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="floatingInput">Bagian</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input autocomplete="one-time-code" type="text" class="form-control spesifikasi" id="spesfikasi" name="spesifikasi" placeholder="Spesifikasi">
                            <label for="floatingInput">Spesifikasi</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="0" autocomplete="one-time-code" onkeyup="formatNumber(this)" type="text" class="form-control harga_umum" id="harga_umum" name="harga_umum" placeholder="Harga Umum">
                            <label for="floatingInput">Harga Umum</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="0" autocomplete="one-time-code" onkeyup="formatNumber(this)" type="text" class="form-control harga_harian" id="harga_harian" name="harga_harian" placeholder="Harga Harian">
                            <label for="floatingInput">Harga Harian</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3" style="height: 50px;">
                            <input value="0" autocomplete="one-time-code" onkeyup="formatNumber(this)" type="text" class="form-control harga_bulanan" id="harga_bulanan" name="harga_bulanan" placeholder="Harga Bulanan">
                            <label for="floatingInput">Harga Bulanan</label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-subtitle-modal">
                <div class="row mt-3">
                    <div class="col-md-6">

                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-add btn-block float-right" onclick="setHarga()">
                            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th width="10">No</th>
                                <th>Bahan Baku</th>
                                <th>Bagian</th>
                                <th>Spesifikasi</th>
                                <th>Harga Umum</th>
                                <th>Harga Harian</th>
                                <th>Harga Bulanan</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="body-detail-table" id="body-detail-table">
                        <?php 
                            $no = 1;    
                            foreach($dataSupplierHarga as $item){ 
                        ?>
                            <tr>
                                <td>
                                    <?= $no++; ?>
                                </td>
                                <td>
                                    <?= $item["barang_name"]; ?>
                                </td>
                                <td>
                                    <?= $item["bagian_name"]; ?>
                                </td>
                                <td>
                                    <?= $item["spesifikasi"]; ?>
                                </td>
                                <td>
                                    <?= "Rp " . number_format(formatter($item["harga_umum"], "STR_TO_FLOAT"), 2, '.', ','); ?>
                                </td>
                                <td>
                                    <?= "Rp " . number_format(formatter($item["harga_harian"], "STR_TO_FLOAT"), 2, '.', ','); ?>
                                </td>
                                <td>
                                    <?= "Rp " . number_format(formatter($item["harga_bulanan"], "STR_TO_FLOAT"), 2, '.', ','); ?>
                                </td>
                                <td>
                                    <button onclick="editHarga('<?= $item['id']; ?>')" class="btn btn-warning posting-spp">
                                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="deleteHarga('<?= $item['id']; ?>')">
                                        <i class="fa fa-trash fa-sm" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const csrfToken = '<?= csrf_token() ?>';

    $('.bahan_baku').select2({
        placeholder: "Pilih Bahan Baku",
        theme: "bootstrap-5"
    })

    $('.bagian').select2({
        placeholder: "Pilih Bagian",
        theme: "bootstrap-5"
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

    var validator = $(".harga-form").validate({
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
                        }
                    });
                }
            })
        }
    })

    const setHarga = function() {
        validator.resetForm();
        validator.reset();
        $(".harga-form")[0].reset()

        $(".id_supplier_harga").val('')
        $(".bahan_baku").val('').change()
        $(".bagian").val('').change()
        $(".harga_umum").val(0)
        $(".harga_harian").val(0)
        $(".harga_bulanan").val(0)
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
                    tag_html += `<tr>`;
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
                    tag_html += "Rp " + Number(item.harga_umum).toLocaleString();
                    tag_html += "</td>";
                    tag_html += "<td>";
                    tag_html += "Rp " + Number(item.harga_harian).toLocaleString();
                    tag_html += "</td>";
                    tag_html += "<td>";
                    tag_html += "Rp " + Number(item.harga_bulanan).toLocaleString();
                    tag_html += "</td>";
                    tag_html += "<td>";
                    tag_html += `<button onclick="editHarga(${item?.id})" class="btn btn-warning posting-spp">
                        <i class="fa fa-pencil fa-sm" aria-hidden="true"></i>
                    </button><button class="btn btn-danger" onclick="deleteHarga(${item?.id})">
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