<a href="#!" class="nav-close"><i class="material-icons">menu</i></a>
<div class="nav-header">
    <h1><b>
            MNJ ASET
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
        <div class="nav-item" data-page="barang">
            <a href="<?= base_url('panel/barang') ?>" class="nav-link"><i class="material-icons">people</i>Data Barang</a>
        </div>
        <div class="nav-item" data-page="kategori">
            <a href="<?= base_url('panel/kategori') ?>" class="nav-link"><i class="material-icons">format_list_bulleted</i>Data Kategori</a>
        </div>
        <div class="nav-item" data-page="barang-masuk">
            <a href="<?= base_url('panel/barang-masuk') ?>" class="nav-link"><i class="material-icons">add_circle</i>Barang Masuk</a>
        </div>
        <div class="nav-item" data-page="barang-keluar">
            <a href="<?= base_url('panel/barang-keluar') ?>" class="nav-link"><i class="material-icons">remove_circle</i>Barang Keluar</a>
        </div>
        <div class="nav-item" data-page="maintenance">
            <a href="<?= base_url('panel/maintenance') ?>" class="nav-link"><i class="material-icons">build</i>Maintenance</a>
        </div>
        <div class="nav-item" data-page="peminjaman">
            <a href="<?= base_url('panel/peminjaman') ?>" class="nav-link">
                <i class="material-icons">assignment</i>Peminjaman</a>
        </div>
        <div class="nav-item" data-page="pengadaan-karyawan">
            <a href="<?= base_url('panel/pengadaan-karyawan') ?>" class="nav-link"><i class="material-icons">shopping_cart</i>Pengadaan</a>
        </div>
        <div class="nav-item" data-page="pengadaan-teknisi">
            <a href="<?= base_url('panel/pengadaan-teknisi') ?>" class="nav-link"><i class="material-icons">shopping_cart</i>Pengadaan Teknisi</a>
        </div>
        <div class="nav-item" data-page="user">
            <a href="<?= base_url('panel/user') ?>" class="nav-link"><i class="material-icons">person</i>Data
                User</a>
        </div>
    <?php endif ?>




    <?php if (auth()->user()->inGroup('hrd')) : ?>
        <div class="nav-item" data-page="barang">
            <a href="<?= base_url('panel/barang') ?>" class="nav-link"><i class="material-icons">people</i>Data Barang</a>
        </div>
        <div class="nav-item" data-page="kategori">
            <a href="<?= base_url('panel/kategori') ?>" class="nav-link"><i class="material-icons">format_list_bulleted</i>Data Kategori</a>
        </div>
        <div class="nav-item" data-page="barang-masuk">
            <a href="<?= base_url('panel/barang-masuk') ?>" class="nav-link"><i class="material-icons">add_circle</i>Barang Masuk</a>
        </div>
        <div class="nav-item" data-page="barang-keluar">
            <a href="<?= base_url('panel/barang-keluar') ?>" class="nav-link"><i class="material-icons">remove_circle</i>Barang Keluar</a>
        </div>
        <div class="nav-item" data-page="maintenance">
            <a href="<?= base_url('panel/maintenance') ?>" class="nav-link"><i class="material-icons">build</i>Maintenance</a>
        </div>
        <div class="nav-item" data-page="peminjaman">
            <a href="<?= base_url('panel/peminjaman') ?>" class="nav-link">
                <i class="material-icons">assignment</i>Peminjaman</a>
        </div>
        <div class="nav-item" data-page="pengadaan-karyawan">
            <a href="<?= base_url('panel/pengadaan-karyawan') ?>" class="nav-link"><i class="material-icons">shopping_cart</i>Pengadaan</a>
        </div>
        <div class="nav-item" data-page="pengadaan-teknisi">
            <a href="<?= base_url('panel/pengadaan-teknisi') ?>" class="nav-link"><i class="material-icons">shopping_cart</i>Pengadaan Teknisi</a>
        </div>

    <?php endif ?>


    <?php if (auth()->user()->inGroup('teknisi')) : ?>

        <div class="nav-item" data-page="maintenance">
            <a href="<?= base_url('panel/maintenance') ?>" class="nav-link"><i class="material-icons">build</i>Maintenance</a>
        </div>
        <div class="nav-item" data-page="peminjaman">
            <a href="<?= base_url('panel/peminjaman') ?>" class="nav-link">
                <i class="material-icons">assignment</i>Peminjaman</a>
        </div>
    <?php endif ?>

    <?php if (auth()->user()->inGroup('karyawan')) : ?>
        <div class="nav-item" data-page="pengadaan-karyawan">
            <a href="<?= base_url('panel/pengadaan-karyawan') ?>" class="nav-link"><i class="material-icons">shopping_cart</i>Pengadaan</a>
        </div>
        <div class="nav-item" data-page="maintenance">
            <a href="<?= base_url('panel/maintenance') ?>" class="nav-link"><i class="material-icons">build</i>Maintenance</a>
        </div>
        <div class="nav-item" data-page="peminjaman">
            <a href="<?= base_url('panel/peminjaman') ?>" class="nav-link">
                <i class="material-icons">assignment</i>Peminjaman</a>
        </div>
    <?php endif ?>
    <div class="nav-item">
        <a href="<?= base_url('logout') ?>" class="nav-link btn-logout"><i class="material-icons">logout</i>Logout</a>
    </div>
</div>