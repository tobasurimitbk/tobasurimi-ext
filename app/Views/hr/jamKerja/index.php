<?= $this->extend('layouts/template'); ?>
<?= $this->Section('content'); ?>

<!-- Begin Page Content -->
<section class="section">
    <div class="section-header">
        <h1>Jam Kerja</h1>
        <!-- <a class="btn btn-show-form btn-add float-right" href="<?= base_url("form-perijinan/create"); ?>">
            <i class="fa fa-plus fa-sm mr-2" aria-hidden="true"></i>Tambah
        </a> -->
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row justify-content-end row-col-spp">
                <?= csrf_field() ?>
                <div class="col-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select divisi" name="divisi" id="divisi" <?= !empty($data) ? ($data->is_posted === true ? 'disabled=true' : '') : ''; ?>>
                            <option value=""></option>
                            <?php
                            if (!empty($dataDivisi)) {
                                foreach ($dataDivisi as $divisi) {
                            ?>
                                    <option value="<?= $employee->id; ?>" <?= !empty($data) ? ($data->divisi === $employee->id ? "selected" : "") : ""; ?>><?= $employee->name; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <label for="floatingInput">Divisi</label>
                    </div>
                </div>


                <div class="col-3">
                    <div class="form-floating mb-3" style="height: 50px;">
                        <select class="form-select shift" name="shift" id="shift" <?= !empty($data) ? ($data->is_posted === true ? 'disabled=true' : '') : ''; ?>>
                            <option value=""></option>
                            <?php
                            if (!empty($dataShift)) {
                                foreach ($dataShift as $shift) {
                            ?>
                                    <option value="<?= $shift->id; ?>" <?= !empty($data) ? ($data->shift === $shift->id ? "selected" : "") : ""; ?>><?= $shift->name; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                        <label for="floatingInput">Shift</label>
                    </div>
                </div>

                <div class="col-3">
                    <button type="button" class="btn btn-primary btn-generate mb-3">Simpan</button>
                </div>


                <div class="col-md-3">
                    <input class="form-control search form-out-search fos-jk mb-3" placeholder="Search" value="" />
                </div>
            </div>
            <div class="row">
                <div class="table-responsive">
                    <table class="table table-bordered nowrap table-hover-tobasurimi dataTable" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-dark">
                            <tr>
                                <th></th>

                                <th onclick="changeSort('employeeNip')" class="sort">NIP</th>

                                <th onclick="changeSort('employeeName')" class="sort">Nama Karyawan</th>

                                <th onclick="changeSort('divisionName')" class="sort">Divisi</th>

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
    let sort = "periode";
    let sortType = "asc";
    let trigger = true;
    let year = 2023;

    var row = 0;

    $(document).ready(function() {
        // EMPLOYEE
        $('.divisi').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.divisi')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.divisi')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.divisi')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        // STATUS
        $('.shift').select2({
            placeholder: "",
            theme: "bootstrap-5"
        })

        //CSS SELECT2 FLOATING LABEL
        $('.shift')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .css('height', ' calc(3.5rem + 2px)');

        $('.shift')
            .parent('div')
            .children('span')
            .children('span')
            .children('span')
            .children('span')
            .css('margin-top', '22px').css('margin-left', '-7px');

        $('.shift')
            .parent('div')
            .find('label')
            .css('z-index', '1');

        $(".search").keyup(function() {
            table.ajax.reload();
        })

        $('#dataTable tbody').on('click', 'tr td:not(.actions):not(.dataTables_empty)', function() {
            const data = table.row(this).data();
            location.replace(`<?= base_url("form-perijinan/id"); ?>/${data.id}`);
        })
    });

    const table = $('.dataTable').DataTable({
        dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>t<'row align-items-start'<'col-md-4'l><'col-md-4 text-center'i><'col-md-4'p>>",
        processing: true,
        serverSide: true,
        ordering: true,
        order: [
            [0, 'asc']
        ],
        fixedHeader: true,
        lengthMenu: [
            [25],
            [25],
        ],
        pageLength: 25,
        ajax: {
            url: "<?= base_url("form-perijinan/all"); ?>",
            dataSrc: "data",
            data: function(data) {
                data.search = $(".search").val();
                // data.divisi = $(".divisi").val();
                // data.shift = $(".shift").val();
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
            data: "",
            className: "text-center"
        }, {
            data: "employeeNip",
            className: "text-center"
        }, {
            data: "employeeName",
            className: "text-center"
        }, {
            data: "divisi",
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