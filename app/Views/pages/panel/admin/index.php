<?php

/** @var \CodeIgniter\View\View $this */
?>

<?= $this->extend('layouts/panel/main') ?>
<?= $this->section('main') ?>
<h1 class="page-title">Dashboard</h1>
<div style="overflow:auto">
    <div class="container">
        <div class="row">
            <div class="col s12 m6 l3">
                <div class="count-card">
                    <div class="count-number" data-entity="barang">0</div>
                    <div class="count-desc">
                        <p><b>Jumlah</b></p>
                        <p>Barang</p>
                    </div>
                </div>
            </div>
            <div class="col s12 m6 l3">
                <div class="count-card">
                    <div class="count-number" data-entity="kategori">0</div>
                    <div class="count-desc">
                        <p><b>Jumlah</b></p>
                        <p>Kategori</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col s12">
                <div class="text-card">

                    <p>
                        <b>Selamat datang di aplikasi inventaris barang</b>
                        <br>
                        Aplikasi ini membantu dalam proses manajemen inventaris barang perusahaan.
                    </p>


                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>