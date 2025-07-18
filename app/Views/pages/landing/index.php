<?php

/** @var \CodeIgniter\View\View $this */
?>

<?= $this->extend('layouts/landing/main') ?>
<?= $this->section('main') ?>

<div class="page" id="hero">
    <div class="herotext-wrapper">
        <h1>Sistem Rekomendasi Wisata</h1>
        <div class="desc">
            <h2>
                Sistem Rekomendasi Wisata
            </h2>
            <br>
            <p>
                Sistem Rekomendasi Wisata adalah aplikasi yang membantu pengguna menemukan tempat wisata terbaik berdasarkan kriteria tertentu. Dengan menggunakan algoritma klasterisasi dan perengkingan, sistem ini memberikan rekomendasi yang sesuai dengan preferensi pengguna.
            </p>
        </div>
        <a href="#" class="next-page"></a>
    </div>
    <div class="hero-wrapper">
        <img src="<?= base_url('img/landing.webp') ?>" class="hero" alt="hero">
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