<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
<div class="section-header">
    <h1 class="title-name">Tambah</h1>
    <div class="col-button-tambah-spp">
        <a class="btn btn-hide-form btn-discard float-right" href="<?= base_url("retur-pembelian"); ?>">
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
            <input autocomplete="one-time-code" type="hidden" class="id" name="id" id="id" />
            <?= csrf_field() ?>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <div class="input-group input-group-password">
                            <div class="form-floating mb-3" style="height: 50px;">
                                <input autocomplete="one-time-code" type="text" class="form-control no" id="no" name="no" placeholder="No. Retur">
                                <label for="floatingInput">No. Retur</label>
                            </div>
                            <div class="input-generate input-group-prepend group-prepend-password align-items-center">
                                <input autocomplete="one-time-code" style="z-index: 99; margin-bottom: 10px; margin-left: -30px;" class="auto_generate" id="auto_generate" name="auto_generate" type="checkbox" onchange="changeStatus()">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select po_no" name="po_no" id="po_no">
                            <option value=""></option>
                        </select>
                        <label for="floatingInput">No. PO</label>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</section>

<script>
    const changeStatus = function()
    {
        let value = document.getElementById('auto_generate').checked ? true : false;

        if(value)
        {
            $(".no").attr("readonly", true);
            $(".no").val("AUTO GENERATE");
        }
        else
        {
            $(".no").attr("readonly", false);
            $(".no").val("");
        }
    }
</script>
<?= $this->endSection(); ?>