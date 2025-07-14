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
            'username' => 'karyawan',
            'name' => 'karyawan',
        ])->setEmailIdentity([
            'email' => 'karyawan@gmail.com',
            'password' => "password",
        ])->addGroup('karyawan')->activate();
        PenggunaModel::create([
            'username' => 'hrd',
            'name' => 'hrd',
        ])->setEmailIdentity([
            'email' => 'hrd@gmail.com',
            'password' => "password",
        ])->addGroup('hrd')->activate();
        PenggunaModel::create([
            'username' => 'teknisi',
            'name' => 'teknisi',
        ])->setEmailIdentity([
            'email' => 'teknisi@gmail.com',
            'password' => "password",
        ])->addGroup('teknisi')->activate();


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
        ]);
    }
}
