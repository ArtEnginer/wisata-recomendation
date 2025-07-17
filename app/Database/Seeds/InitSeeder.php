<?php

namespace App\Database\Seeds;

use App\Models\PenggunaModel;
use CodeIgniter\Database\Seeder;

class InitSeeder extends Seeder
{
    public function run()
    {

        PenggunaModel::create([
            'username' => 'admin',
            'name' => 'Admin',
        ])->setEmailIdentity([
            'email' => 'admin@gmail.com',
            'password' => "password",
        ])->addGroup('admin')->activate();
        PenggunaModel::create([
            'username' => 'pimpinan',
            'name' => 'pimpinan',
        ])->setEmailIdentity([
            'email' => 'pimpinan@gmail.com',
            'password' => "password",
        ])->addGroup('pimpinan')->activate();
        PenggunaModel::create([
            'username' => 'wisatawan',
            'name' => 'wisatawan',
        ])->setEmailIdentity([
            'email' => 'wisatawan@gmail.com',
            'password' => "password",
        ])->addGroup('wisatawan')->activate();
     
        // wisata
        $this->db->table('wisata')->insertBatch([
            [
                'kode' => '001',
                'nama' => 'Wisata Alam',
                'alamat' => 'Jl. Raya No. 1',
                'deskripsi' => 'Wisata alam yang indah',
                'gambar' => 'wisata-alam.jpg',
                'latitude' => '-7.123456',
                'longitude' => '112.123456',
                'klaster' => null,
            ],
            [
                'kode' => '002',
                'nama' => 'Wisata Budaya',
                'alamat' => 'Jl. Raya No. 2',
                'deskripsi' => 'Wisata budaya yang menarik',
                'gambar' => 'wisata-budaya.jpg',
                'latitude' => '-7.654321',
                'longitude' => '112.654321',
                'klaster' =>  null,
            ],
            [
                'kode' => '003',
                'nama' => 'Wisata Sejarah',
                'alamat' => 'Jl. Raya No. 3',
                'deskripsi' => 'Wisata sejarah yang kaya akan budaya',
                'gambar' => 'wisata-sejarah.jpg',
                'latitude' => '-7.987654',
                'longitude' => '112.987654',
                'klaster' =>  null,
            ],
            [
                'kode' => '004',
                'nama' => 'Wisata Kuliner',
                'alamat' => 'Jl. Raya No. 4',
                'deskripsi' => 'Wisata kuliner yang lezat',
                'gambar' => 'wisata-kuliner.jpg',
                'latitude' => '-7.456789',
                'longitude' => '112.456789',
                'klaster' =>  null,
            ],
        ]);


        $this->db->table('kriteria_klasterisasi')->insertBatch([
            [
                'kode' => 'K001',
                'nama' => 'Keragaman Keunikan',
                'deskripsi' => 'Kriteria ini menilai keragaman dan keunikan dari objek wisata.',
            ],
            [
                'kode' => 'K002',
                'nama' => 'Daya Tarik',
                'deskripsi' => 'Kriteria ini menilai daya tarik dari objek wisata.',
            ],
            [
                'kode' => 'K003',
                'nama' => 'Nilai Historis / Budaya',
                'deskripsi' => 'Kriteria ini menilai nilai historis atau budaya dari objek wisata.',
            ],
            [
                'kode' => 'K004',
                'nama' => 'Jumlah Pengunjung',
                'deskripsi' => 'Kriteria ini menilai jumlah pengunjung dari objek wisata.',
            ],
            [
                'kode' => 'K005',
                'nama' => 'Popularitas',
                'deskripsi' => 'Kriteria ini menilai popularitas dari objek wisata.',
            ],
        ]);

        $this->db->table('kriteria_perengkingan')->insertBatch([
            [
                'kode' => 'P001',
                'nama' => 'Lokasi Strategis',
                'deskripsi' => 'Kriteria ini menilai lokasi strategis dari objek wisata.',
            ],
            [
                'kode' => 'P002',
                'nama' => 'Fasilitas',
                'deskripsi' => 'Kriteria ini menilai fasilitas yang tersedia di objek wisata.',
            ],
            [
                'kode' => 'P003',
                'nama' => 'Biaya',
                'deskripsi' => 'Kriteria ini menilai biaya yang diperlukan untuk mengunjungi objek wisata.',
            ],
            [
                'kode' => 'P004',
                'nama' => 'Keamanan',
                'deskripsi' => 'Kriteria ini menilai tingkat keamanan di objek wisata.',
            ],
        ]);
    }
}
