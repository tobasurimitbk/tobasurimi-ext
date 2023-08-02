<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Payroll</h1>
        <!-- <button class="btn btn-show-form btn-add float-right" data-btn="create-modal">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </button> -->
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select formula" id="formula" name="formula" aria-label="Floating label select example">
                            <option value=""></option>
                            <option value="ok">ok</option>
                        </select>
                        <label for="floatingInput">Formula</label>
                    </div>
                </div>

                <div class="col">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <input class="form-control komponen" placeholder="Search" value="" disabled />
                        <label for="floatingInput">Komponen</label>
                    </div>
                </div>

                <div class="col">
                    <button class="btn btn-primary btn-generate">
                        Generate
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end mb-3">
                <div class="col-md-2">
                    <input class="form-control search form-out-search" placeholder="Search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th onclick="changeSort('nip')" class="sort">NIP</th>
                                <th onclick="changeSort('name')" class="sort">Nama Lengkap</th>
                                <th onclick="changeSort('divisionName')" class="sort">Divisi</th>
                                <th onclick="changeSort('email')" class="sort">Email</th>
                                <th onclick="changeSort('phone_no')" class="sort">No. Telepon</th>
                                <th onclick="changeSort('address')" class="sort">Alamat</th>
                                <th onclick="changeSort('dob')" class="sort">Tanggal Lahir</th>
                                <th onclick="changeSort('gender')" class="sort">Jenis Kelamin</th>
                                <th onclick="changeSort('acc_no')" class="sort">No. Rekening</th>
                                <th onclick="changeSort('status')" class="sort">Status</th>
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
    $(document).ready(function() {
        // FORMULA
        $('.formula').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.formula')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.formula')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.formula')
            .parent('div')
            .find('label')
            .css('z-index', '1');
    })


    const changeSort = function(val) {
        if (sort !== val) {
            sortType = "asc";
            sort = val;
        } else {
            sortType = sortType === "asc" ? "desc" : "asc";
        }
    }
</script>

<?= $this->endSection(); ?>