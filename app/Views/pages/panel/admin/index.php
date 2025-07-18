<?= $this->extend('layouts/panel/main') ?>
<?= $this->section('main') ?>
<h1 class="page-title">Rekomendasi Wisata</h1>
<div style="overflow:auto">
    <div class="container">
        <h4 class="center-align">Sistem Rekomendasi Wisata</h4>

        <div class="row">
            <div class="col s12">
                <ul class="tabs">
                    <li class="tab col s2"><a href="#map-tab">Peta</a></li>
                    <li class="tab col s2"><a href="#cluster-tab">Klasterisasi</a></li>
                    <li class="tab col s2"><a href="#distance-tab">Jarak</a></li>
                    <li class="tab col s2"><a href="#recommend-tab">Rekomendasi</a></li>
                    <li class="tab col s2"><a href="#accuracy-tab">Akurasi</a></li>
                    <?php if (auth()->user()->inGroup('pimpinan')) : ?>
                        <li class="tab col s2"><a href="#laporan-tab">Laporan</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div id="map-tab" class="col s12">
                <div id="map" style="height: 500px;"></div>
                <div class="input-field">
                    <input id="location-name" type="text" placeholder="Nama Lokasi">
                    <label for="location-name">Nama Lokasi</label>
                </div>
                <div class="input-field">
                    <input id="user-lat" type="number" step="any" placeholder="-7.123456">
                    <label for="user-lat">Latitude</label>
                </div>
                <div class="input-field">
                    <input id="user-lng" type="number" step="any" placeholder="112.123456">
                    <label for="user-lng">Longitude</label>
                </div>
                <div class="card-panel teal lighten-4">Klik pada peta atau gunakan pencarian di pojok kiri atas peta</div>
            </div>

            <div id="cluster-tab" class="col s12">
                <a class="waves-effect waves-light btn green" id="btn-cluster">Klasterisasi (K-Means)</a>
                <div id="cluster-results" class="section"></div>
                <div id="cluster-details" class="section"></div>
                <div id="calculation-steps" class="card-panel grey lighten-3"></div>
            </div>

            <div id="distance-tab" class="col s12">
                <a class="waves-effect waves-light btn blue" id="btn-shortest-path">Hitung Rute Terpendek</a>

                <div id="distance-results" class="section"></div>
            </div>

            <div id="recommend-tab" class="col s12">
                <a class="waves-effect waves-light btn amber" id="btn-recommend">Rekomendasi</a>
                <div id="recommendation-results" class="section"></div>
                <div id="wp-calculation" class="card-panel grey lighten-3"></div>
            </div>

            <div id="accuracy-tab" class="col s12">
                <canvas id="accuracyChart" height="100"></canvas>
            </div>

            <div id="laporan-tab" class="col s12">
                <a class="waves-effect waves-light btn red" id="btn-generate-report">Generate Laporan</a>
                <div id="report-results" class="section"></div>
                <div id="report-details" class="card-panel grey lighten-3"></div>
                <div id="report-download" class="section"></div>
            </div>
        </div>
    </div>

</div>

<!-- Modal Structure -->
<div id="calculation-modal" class="modal modal-fixed-footer">
    <div class="modal-content">
        <h4>Detail Perhitungan</h4>
        <div class="modal-body"></div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-close waves-effect waves-green btn-flat">Tutup</a>
    </div>
</div>
<?= $this->endSection() ?>