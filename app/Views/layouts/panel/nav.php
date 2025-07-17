<a href="#!" class="nav-close"><i class="material-icons">menu</i></a>
<div class="nav-header">
    <h1><b>
            REKOMENDASI WISATA
        </b></h1>

    <h3><b>
            <?= auth()->user()->inGroup('admin') ? 'Admin' : (auth()->user()->inGroup('hrd') ? 'HRD' : (auth()->user()->inGroup('teknisi') ? 'Teknisi
            ' : (auth()->user()->inGroup('karyawan') ? 'Karyawan' : (auth()->user()->inGroup('guru') ? 'Guru' : 'User')))) ?>
        </b></h3>
</div>
<div class="nav-list">
    <div class="nav-item" data-page="dashboard">
        <a href="<?= base_url('panel') ?>" class="nav-link"><i class="material-icons">dashboard</i>Dashboard</a>
    </div>
    <?php if (auth()->user()->inGroup('admin')) : ?>
        <div class="nav-item" data-page="wisata">
            <a href="<?= base_url('panel/wisata') ?>" class="nav-link"><i class="material-icons">
                    place</i>Data Wisata</a>
        </div>

        <div class="nav-item" data-page="kriteria-klasterisasi">
            <a href="<?= base_url('panel/kriteria-klasterisasi') ?>" class="nav-link"><i class="material-icons">
                    book</i>Kriteria Klasterisasi</a>
        </div>

        <div class="nav-item" data-page="nilai-kriteria-klasterisasi">
            <a href="<?= base_url('panel/nilai-kriteria-klasterisasi') ?>" class="nav-link"><i class="material-icons">
                    assessment</i>Nilai Kriteria Klasterisasi</a>
        </div>

        <div class="nav-item" data-page="kriteria-perengkingan">
            <a href="<?= base_url('panel/kriteria-perengkingan') ?>" class="nav-link"><i class="material-icons">
                    book</i>Kriteria Perengkingan</a>
        </div>
        <div class="nav-item" data-page="nilai-kriteria-perengkingan">
            <a href="<?= base_url('panel/nilai-kriteria-perengkingan') ?>" class="nav-link"><i class="material-icons">
                    assessment</i>Nilai Kriteria Perengkingan</a>
        </div>


        <div class="nav-item" data-page="user">
            <a href="<?= base_url('panel/user') ?>" class="nav-link"><i class="material-icons">person</i>Data
                User</a>
        </div>
    <?php endif ?>





    <?php if (auth()->user()->inGroup('wisatawan')) : ?>


    <?php endif ?>
    <div class="nav-item">
        <a href="<?= base_url('logout') ?>" class="nav-link btn-logout"><i class="material-icons">logout</i>Logout</a>
    </div>
</div>