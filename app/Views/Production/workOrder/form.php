<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1 class="title-name">Tambah</h1>
    <div class="col-button-tambah-spp">
        <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("work-order"); ?>">
            Batal
        </a>
        <button class="btn btn-show-form btn-save float-right btn-submit-form">
            Simpan
        </button>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <form class="create-form form-add-spp" role="form" method="POST" enctype="multipart/form-data">
            <input type="hidden" value="<?= !empty($dataWorkOrders) ? $dataWorkOrders->id : ""; ?>" class="id" name="id" id="id" />
            <?= csrf_field() ?>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input type="text" value="<?= !empty($dataWorkOrders) ? $dataWorkOrders->wo_no : ""; ?>" class="form-control wo_no" id="wo_no" name="wo_no" placeholder="Kode Produksi">
                                <label for="floatingInput">Kode Produksi</label>
                            </div>
                            <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                <input style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select barang_id" name="barang_id" id="barang_id" aria-label="Floating label select example">
                            <option value=""></option>
                            <?php
                                if (!empty($dataBarang)) {
                                    foreach ($dataBarang as $barang) {
                                ?>
                                        <option <?= !empty($dataWorkOrders) ? ($dataWorkOrders->barang_id === $barang->id ? "selected" : "") : ""; ?> value="<?= $barang->id; ?>"><?= $barang->kode_barang; ?> - <?= $barang->nama_barang; ?></option>
                                <?php
                                    }
                                }
                            ?>
                        </select>
                        <label for="floatingInput">Barang</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input type="text" value="<?= !empty($dataWorkOrders) ? formatter($dataWorkOrders->production_amt, "STR_TO_INT") : ""; ?>"  oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control production_amt" name="production_amt" id="production_amt" placeholder="Hasil">
                        <label for="floatingInput">Hasil</label>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</section>

<script>
const csrfToken = '<?= csrf_token() ?>';

$(document).ready(function() {
    // AJU DOCUMENT TYPE
    $('.barang_id').select2({
        placeholder: "",
        theme: "bootstrap-5",
    })

    //CSS SELECT2 FLOATING LABEL
    $('.barang_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .css('height', ' calc(3.5rem + 2px)');

    $('.barang_id')
        .parent('div')
        .children('span')
        .children('span')
        .children('span')
        .children('span')
        .css('margin-top', '22px').css('margin-left', '-7px');

    $('.barang_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    $('.barang_id')
        .parent('div')
        .find('label')
        .css('z-index', '1');

    var validator = $(".create-form").validate({
        rules: {
            barang_id: {
                required: true
            },
            production_amt: {
                required: true
            }
        },
        messages: {
            barang_id: {
                required: "Barang wajib diisi"
            },
            production_amt: {
                required: "Hasil wajib diisi"
            }
        },
        errorElement: 'span',
        errorClass: 'text-danger',
        errorPlacement: function(error, element) {
            var elem = $(element);
            console.log(elem);
            if (elem.hasClass("multiple_po_id")) {
                element = $(".select2-selection--multiple").parent();
                error.insertAfter(element);
            } else if (elem.hasClass("select2-hidden-accessible")) {
                element = $("#select2-" + elem.attr("id") + "-container").parent(); 
                error.insertAfter(element);
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).closest('.col-md-6').addClass('has-error');
            $(element).addClass('select-class');

        },
        unhighlight: function(element) {
            $(element).closest('.col-md-6').removeClass('has-error');
            $(element).removeClass('select-class');
        },
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
                    setLoading()
                    let data = new FormData(document.querySelector(".create-form"));

                    let id = $(".id").val();
                    // UPDATE
                    if(id)
                    {
                        $.ajax({
                            url: "<?= base_url("work-order/update"); ?>",
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
                                        window.location.href = "<?= base_url("work-order"); ?>" + "/id/" + id;
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
                    else
                    {
                        $.ajax({
                            url: "<?= base_url("work-order/save"); ?>",
                            data: data,
                            beforeSend: function(xhr) {
                                xhr.setRequestHeader('X-CSRF-Token', csrf.val());
                            },
                            method: "POST",
                            dataType: "json",
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                console.log(response)
                                csrf.val(response.token);
                                if (response.status) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: response.message,
                                        confirmButtonColor: '#4e73df',
                                    })
                                    .then(() => {
                                        window.location.href = "<?= base_url("work-order"); ?>" + "/id/" + response.id;
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
})

const changeStatus = function()
{
    let value = document.getElementById('auto_generate').checked ? true : false;

    if(value)
    {
        $(".wo_no").attr("readonly", true);
        $(".wo_no").val("AUTO GENERATE");
    }
    else
    {
        $(".wo_no").attr("readonly", false);
        $(".wo_no").val("");
    }
}
</script>

<?= $this->endSection(); ?>