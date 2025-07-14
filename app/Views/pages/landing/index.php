<?php

/** @var \CodeIgniter\View\View $this */
?>

<?= $this->extend('layouts/landing/main') ?>
<?= $this->section('main') ?>

<div class="page" id="hero">
    <div class="herotext-wrapper">
        <h1>Sistem Manajemen Aset Perusahaan</h1>
        <div class="desc">
            <h2>
                Manajemen Aset Perusahaan
            </h2>
            <br>
            <p>
                Sistem Manajemen Aset Perusahaan adalah sistem yang dirancang untuk membantu perusahaan dalam mengelola aset-asetnya dengan lebih efisien dan efektif. Sistem ini mencakup berbagai fitur yang memungkinkan perusahaan untuk melacak, memantau, dan mengelola aset-aset mereka dengan lebih baik.
            </p>
        </div>
        <a href="#" class="next-page"></a>
    </div>
    <div class="hero-wrapper">
        <img src="<?= base_url('img/hero.webp') ?>" class="hero" alt="hero">
    </div>
</div>
<div class="page">
    <div class="container">
        <div class="row">
            <div class="col s12 m6">
                <h1>Page 2</h1>
            </div>
            <div class="col s12 m6">
                <img src="<?= base_url('img/hero.webp') ?>" class="hero" alt="hero">
            </div>
        </div>
    </div>
</div>

<a href="<?= base_url('panel') ?>" class="btn-login"><i class="material-icons">
        dashboard
    </i> Dashboard</a>
<?= $this->endSection() ?>