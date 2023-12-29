<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "header" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-23/id/header/' . encrypt($lpb->id)) ?>" style="<?= session()->getFlashdata('isCompleteFormHeader') == false ? 'color: red' :  'color:green' ?>">Header</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "entitas" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-23/id/entitas/' . encrypt($lpb->id)) ?>">Entitas</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "dokumen" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-23/id/dokumen/' . encrypt($lpb->id)) ?>">Dokumen</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "pengangkut" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-23/id/pengangkut/' . encrypt($lpb->id)) ?>">Pengangkut</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "kemasan-peti-kemas" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-23/id/kemasan-peti-kemas/' . encrypt($lpb->id)) ?>">Kemasan & Peti Kemas</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "transaksi" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-23/id/transaksi/' . encrypt($lpb->id)) ?>">Transaksi</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "barang" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-23/id/barang/' . encrypt($lpb->id)) ?>">Barang</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "pungutan" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-23/id/pungutan/' . encrypt($lpb->id)) ?>">Pungutan</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= request()->uri->getSegment(3) === "pernyataan" ? 'active' : '' ?>" href="<?= base_url('bea-cukai-bc-23/id/pernyataan/' . encrypt($lpb->id)) ?>" style="<?= session()->getFlashdata('isCompleteFormPernyataan') == false ? 'color: red' :  'color:green' ?>">Pernyataan</a>
    </li>
</ul>