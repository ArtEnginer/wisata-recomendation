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

            // Keraton Surakarta Hadiningrat	-75777373	110825335
            // Pura Mangkunegaran	-75668958	1108203116
            // Kampung Batik Laweyan	-75696487	1107951318
            // Masjid Agung Keraton Surakarta	-7574398	1108240187
            // Taman Balekambang	-75523052	1108050936
            // Solo Safari	-75646417	110856022
            // Taman Sriwedari	-75685598	1108104205
            // The Heritage Palace	-75547544	1107522454
            // Tumurun Private Museum	-75704012	1108138152
            // Masjid Raya Sheikh Zayed	-75547278	1108241381

            [
                'kode' => '001',
                'nama' => 'Keraton Surakarta Hadiningrat',
                'alamat' => 'Jl. Keraton Surakarta Hadiningrat No. 1',
                'deskripsi' => 'Keraton Surakarta Hadiningrat adalah istana kerajaan yang kaya akan sejarah dan budaya Jawa.',
                'gambar' => 'keraton-surakarta.jpg',
                'latitude' => '-7.5737373',
                'longitude' => '110.825335',
                'klaster' =>  null,
            ],


            [
                'kode' => '002',
                'nama' => 'Pura Mangkunegaran',
                'alamat' => 'Jl. Pura Mangkunegaran No. 1',
                'deskripsi' => 'Pura Mangkunegaran adalah pura yang merupakan tempat tinggal keluarga kerajaan Mangkunegaran.',
                'gambar' => 'pura-mangkunegaran.jpg',
                'latitude' => '-7.5668958',
                'longitude' => '110.8203116',
                'klaster' =>  null,
            ],

            [
                'kode' => '003',
                'nama' => 'Kampung Batik Laweyan',
                'alamat' => 'Jl. Batik Laweyan No. 1',
                'deskripsi' => 'Kampung Batik Laweyan adalah kawasan yang terkenal dengan kerajinan batiknya.',
                'gambar' => 'kampung-batik-laweyan.jpg',
                'latitude' => '-7.5696487',
                'longitude' => '110.7951318',
                'klaster' =>  null,
            ],
            [
                'kode' => '004',
                'nama' => 'Masjid Agung Keraton Surakarta',
                'alamat' => 'Jl. Masjid Agung No. 1',
                'deskripsi' => 'Masjid Agung Keraton Surakarta adalah masjid yang terletak di kompleks Keraton Surakarta.',
                'gambar' => 'masjid-agung-keraton-surakarta.jpg',
                'latitude' => '-7.574398',
                'longitude' => '110.8240187',
                'klaster' =>  null,
            ],
            [
                'kode' => '005',
                'nama' => 'Taman Balekambang',
                'alamat' => 'Jl. Taman Balekambang No. 1',
                'deskripsi' => 'Taman Balekambang adalah taman kota yang indah dan cocok untuk bersantai.',
                'gambar' => 'taman-balekambang.jpg',
                'latitude' => '-7.5523052',
                'longitude' => 110.8050936,
                'klaster' =>  null,
            ],
            [
                'kode' => "006",
                "nama" => "Solo Safari",
                "alamat" => "Jl. Solo Safari No. 1",
                "deskripsi" => "Solo Safari adalah taman safari yang menawarkan pengalaman melihat satwa liar secara langsung.",
                "gambar" => "solo-safari.jpg",
                "latitude" => "-7.5646417",
                "longitude" => "110.856022",
                'klaster' =>  null,
            ],
            [
                'kode' => "007",
                "nama" => "Taman Sriwedari",
                "alamat" => "Jl. Taman Sriwedari No. 1",
                "deskripsi" => "Taman Sriwedari adalah taman yang sering digunakan untuk pertunjukan seni dan budaya.",
                "gambar" => "taman-sriwedari.jpg",
                "latitude" => "-7.5685598",
                "longitude" => "110.8104205",
                'klaster' =>  null,
            ],
            [
                'kode' => "008",
                "nama" => "The Heritage Palace",
                "alamat" => "Jl. The Heritage Palace No. 1",
                "deskripsi" => "The Heritage Palace adalah museum yang menampilkan koleksi seni dan budaya Jawa.",
                "gambar" => "the-heritage-palace.jpg",
                "latitude" => "-7.5547544",
                "longitude" => "110.7522454",
                'klaster' =>  null,
            ],
            [
                'kode' => "009",
                "nama" => "Tumurun Private Museum",
                "alamat" => "Jl. Tumurun No. 1",
                "deskripsi" => "Tumurun Private Museum adalah museum pribadi yang menyimpan koleksi seni yang berharga.",
                "gambar" => "tumurun-private-museum.jpg",
                "latitude" => "-7.5704012",
                "longitude" => "110.8138152",
                'klaster' =>  null,
            ],
            [
                'kode' => '010',
                'nama' => 'Masjid Raya Sheikh Zayed',
                'alamat' => 'Jl. Masjid Raya Sheikh Zayed No. 1',
                'deskripsi' => 'Masjid Raya Sheikh Zayed adalah masjid megah yang menjadi salah satu ikon kota.',
                'gambar' => 'masjid-raya-sheikh-zayed.jpg',
                'latitude' => '-7.5547278',
                'longitude' => '110.8241381',
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
