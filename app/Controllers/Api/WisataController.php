<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApi;
use App\Models\WisataModel;

class WisataController extends BaseApi
{
    protected $modelName = WisataModel::class;
    protected $load = ['nilaiKriteriaKlasterisasi'];
    public function validateCreate(&$request)

    {
        return $this->validate([
            'nama' => 'required',
            'kode' => 'required|is_unique[wisata.kode]',
            'gambar' => 'uploaded[gambar]|max_size[gambar,2048]|is_image[gambar]|mime_in[gambar,image/jpg,image/jpeg,image/gif,image/png]',
        ]);
    }


    public function beforeCreate(&$request)
    {
        $image = $this->request->getFile('gambar');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $namagambar = $image->getRandomName();
            $image->move(WRITEPATH . 'uploads', $namagambar);
            $request->gambar = $namagambar;
        }
    }


    public function beforeUpdate(&$request)
    {
        $image = $this->request->getFile('gambar');

        if ($image && $image->isValid() && !$image->hasMoved()) {
            $namagambar = $image->getRandomName();
            $image->move(WRITEPATH . 'uploads', $namagambar);
            $request->gambar = $namagambar;
        }
    }

    public function clustering()
    {
        $wisataList = WisataModel::with('nilaiKriteriaKlasterisasi')->get();
        $data = [];
        $kodeMapping = [];
        $originalValues = [];

        foreach ($wisataList as $index => $wisata) {
            $kode = $wisata->kode;
            $nama = $wisata->nama;
            $nilaiArray = [];

            foreach ($wisata->nilaiKriteriaKlasterisasi as $nilai) {
                $nilaiArray[] = (float) $nilai->nilai;
            }

            if (!empty($nilaiArray)) {
                $data[] = $nilaiArray;
                $originalValues[] = $nilaiArray;
                $kodeMapping[$index] = [
                    'kode' => $kode,
                    'nama' => $nama,
                ];
            }
        }

        // Normalisasi
        $maxValues = [];
        foreach ($data as $row) {
            foreach ($row as $i => $val) {
                $maxValues[$i] = max($maxValues[$i] ?? 0, $val);
            }
        }

        $normalizedData = [];
        $normalisasiLog = [];

        foreach ($data as $idx => $row) {
            $normRow = [];
            foreach ($row as $i => $val) {
                $norm = $maxValues[$i] > 0 ? $val / $maxValues[$i] : 0;
                $normRow[] = $norm;
            }
            $normalizedData[] = $normRow;

            $normalisasiLog[] = [
                'no' => $idx + 1,
                'kode' => $kodeMapping[$idx]['kode'],
                'nama' => $kodeMapping[$idx]['nama'],
                'original' => $row,
                'normalized' => $normRow
            ];
        }

        // Jalankan KMeans
        $k = 3;
        $result = $this->kMeans($normalizedData, $k);

        // Beri label klaster
        $labels = ['rendah', 'sedang', 'tinggi'];
        $centroidSums = array_map(fn($c) => array_sum($c), $result['centroids']);
        asort($centroidSums);
        $orderedIndices = array_keys($centroidSums);
        $clusterKategori = [];
        foreach ($orderedIndices as $i => $centroidIndex) {
            $clusterKategori[$centroidIndex] = $labels[$i];
        }

        // Update DB dan hasil akhir
        $finalAssignments = [];
        foreach ($result['clusters'] as $i => $clusterIndex) {
            $kode = $kodeMapping[$i]['kode'];
            $kategori = $clusterKategori[$clusterIndex];
            WisataModel::where('kode', $kode)->update(['klaster' => $kategori]);

            $finalAssignments[] = [
                'no' => $i + 1,
                'kode' => $kode,
                'nama' => $kodeMapping[$i]['nama'],
                'original' => $originalValues[$i],
                'normalized' => $normalizedData[$i],
                'cluster' => $clusterIndex,
                'label' => $kategori
            ];
        }

        return $this->respond([
            'message' => 'Clustering completed successfully',
            'normalisasi' => $normalisasiLog,
            'log' => $result['log'],
            'assignments' => $finalAssignments
        ]);
    }


    private function kMeans($data, $k = 3, $maxIterations = 100)
    {
        $centroids = [];
        $clusters = [];
        $prevClusters = [];
        $log = [];

        $indices = array_rand($data, $k);
        foreach ($indices as $index) {
            $centroids[] = $data[$index];
        }

        $log[] = ['iteration' => 0, 'centroids' => $centroids];

        for ($iteration = 1; $iteration <= $maxIterations; $iteration++) {
            $clusters = [];
            $distances = [];

            foreach ($data as $i => $point) {
                $minDist = INF;
                $assignedCluster = 0;
                $distRow = [];

                foreach ($centroids as $j => $centroid) {
                    $dist = $this->euclideanDistance($point, $centroid);
                    $distRow["C" . ($j + 1)] = $dist;

                    if ($dist < $minDist) {
                        $minDist = $dist;
                        $assignedCluster = $j;
                    }
                }

                $clusters[$i] = $assignedCluster;
                $distances[$i] = $distRow;
            }

            if ($clusters === $prevClusters) break;
            $prevClusters = $clusters;

            $newCentroids = array_fill(0, $k, []);
            $counts = array_fill(0, $k, 0);

            foreach ($clusters as $i => $clusterIndex) {
                foreach ($data[$i] as $d => $value) {
                    $newCentroids[$clusterIndex][$d] = ($newCentroids[$clusterIndex][$d] ?? 0) + $value;
                }
                $counts[$clusterIndex]++;
            }

            foreach ($newCentroids as $j => $centroid) {
                if ($counts[$j] > 0) {
                    foreach ($centroid as $d => $sum) {
                        $newCentroids[$j][$d] = $sum / $counts[$j];
                    }
                } else {
                    $newCentroids[$j] = $data[array_rand($data)];
                }
            }

            $centroids = $newCentroids;
            $log[] = [
                'iteration' => $iteration,
                'centroids' => $centroids,
                'clusters' => $clusters,
                'distances' => $distances
            ];
        }

        return ['clusters' => $clusters, 'centroids' => $centroids, 'log' => $log];
    }



    private function euclideanDistance($a, $b)
    {
        $sum = 0;
        foreach ($a as $i => $val) {
            $sum += pow($val - $b[$i], 2);
        }
        return sqrt($sum);
    }
}
