<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApi;
use App\Models\NilaiKriteriaPerengkinganModel;

class NilaiKriteriaPerengkinganController extends BaseApi
{
    protected $modelName = NilaiKriteriaPerengkinganModel::class;
    protected $load = ['kriteriaPerengkingan', 'wisata'];

    public function store()
    {
        $wisata_kodes = $this->request->getPost('wisata_kode');
        $nilai_data = $this->request->getPost('nilai');

        foreach ($wisata_kodes as $kode_wisata) {
            foreach ($nilai_data[$kode_wisata] as $kode_kriteria => $nilai) {
                NilaiKriteriaPerengkinganModel::updateOrCreate(
                    [
                        'kriteria_perengkingan_kode' => $kode_kriteria,
                        'wisata_kode' => $kode_wisata,
                    ],
                    [
                        'nilai' => $nilai,
                    ]
                );
            }
        }
    }


    public function storeupdate()
    {
        $wisataKode = $this->request->getPost('wisata_kode');
        $nilaiData = $this->request->getPost('nilai');

        if (!$wisataKode || !$nilaiData) {
            return $this->failValidationErrors("Data tidak lengkap.");
        }

        // Hapus semua nilai lama dulu
        NilaiKriteriaPerengkinganModel::where('wisata_kode', $wisataKode)->delete();

        // Insert baru pakai create (Eloquent-style)
        foreach ($nilaiData as $kriteriaKode => $nilai) {
            NilaiKriteriaPerengkinganModel::create([
                'wisata_kode' => $wisataKode,
                'kriteria_Perengkingan_kode' => $kriteriaKode,
                'nilai' => $nilai,
            ]);
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'Nilai berhasil diperbarui',
        ]);
    }



    public function grouped()
    {

        $data = NilaiKriteriaPerengkinganModel::with(['kriteriaPerengkingan', 'wisata'])
            ->get()
            ->groupBy('wisata.kode')
            ->map(function ($items, $kode_wisata) {
                $wisata = $items->first()->wisata;
                $nilai = $items->pluck('nilai', 'kriteriaPerengkingan.kode')->toArray();
                return [
                    'nama' => $wisata->nama,
                    'kode' => $kode_wisata,
                    'nilai' => $nilai,
                ];
            })->values();

        return $this->respond($data);
    }
}
