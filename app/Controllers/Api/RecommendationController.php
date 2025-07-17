<?php

namespace App\Controllers\Api;

use App\Controllers\BaseApi;
use App\Models\KriteriaKlasterisasiModel;
use App\Models\WisataModel;

class RecommendationController extends BaseApi
{
    public function index()
    {
        return view('panel/recommendation');
    }

    public function processCluster()
    {


        // Get all tourism data with clustering criteria values
        $wisataData = WisataModel::with('nilaiKriteriaKlasterisasi', 'nilaiKriteriaPerengkingan')->get();
        $kriteriaKlasterisasi = KriteriaKlasterisasiModel::all();

        // Prepare data for clustering
        $clusteringData = [];
        foreach ($wisataData as $wisata) {
            $values = [];
            foreach ($kriteriaKlasterisasi as $kriteria) {
                $nilai = $wisata['nilaiKriteriaKlasterisasi']->where('kriteria_klasterisasi_kode', $kriteria['kode'])->first();
                $values[] = $nilai ? (float)$nilai['nilai'] : 0;
            }
            $clusteringData[] = [
                'id' => $wisata['id'],
                'kode' => $wisata['kode'],
                'nama' => $wisata['nama'],
                'values' => $values
            ];
        }

        // Perform K-Means clustering (3 clusters)
        $clusters = $this->kMeansClustering($clusteringData, 3);

        // Update cluster data in database
        foreach ($clusters as $cluster) {
            WisataModel::where('id', $cluster['id'])
                ->update(['klaster' => $cluster['cluster']]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $clusters
        ]);
    }

    private function kMeansClustering($data, $k, $maxIterations = 100)
    {
        // Initialize centroids randomly
        $centroids = [];
        $randomKeys = array_rand($data, $k);
        foreach ($randomKeys as $key) {
            $centroids[] = $data[$key]['values'];
        }

        $iterations = 0;
        $changed = true;
        $clusters = [];

        while ($changed && $iterations < $maxIterations) {
            $changed = false;
            $newClusters = array_fill(0, $k, []);

            // Assign each point to the nearest centroid
            foreach ($data as $point) {
                $minDistance = PHP_FLOAT_MAX;
                $clusterIndex = 0;

                foreach ($centroids as $index => $centroid) {
                    $distance = $this->euclideanDistance($point['values'], $centroid);
                    if ($distance < $minDistance) {
                        $minDistance = $distance;
                        $clusterIndex = $index;
                    }
                }

                $newClusters[$clusterIndex][] = $point;
                if (!isset($point['cluster']) || $point['cluster'] != $clusterIndex) {
                    $changed = true;
                }

                $point['cluster'] = $clusterIndex;
                $clusters[] = $point;
            }

            // Update centroids
            foreach ($newClusters as $index => $cluster) {
                if (count($cluster) > 0) {
                    $centroids[$index] = $this->calculateCentroid($cluster);
                }
            }

            $iterations++;
        }

        return $clusters;
    }

    private function euclideanDistance($a, $b)
    {
        $sum = 0;
        for ($i = 0; $i < count($a); $i++) {
            $sum += pow($a[$i] - $b[$i], 2);
        }
        return sqrt($sum);
    }

    private function calculateCentroid($cluster)
    {
        $centroid = array_fill(0, count($cluster[0]['values']), 0);
        foreach ($cluster as $point) {
            foreach ($point['values'] as $index => $value) {
                $centroid[$index] += $value;
            }
        }

        foreach ($centroid as &$value) {
            $value /= count($cluster);
        }

        return $centroid;
    }

    public function calculateDistance()
    {
        $userLat = $this->request->getPost('userLat');
        $userLng = $this->request->getPost('userLng');

        $wisataModel = new \App\Models\WisataModel();
        $wisataData = $wisataModel->where('klaster IS NOT NULL')->findAll();

        $distances = [];
        foreach ($wisataData as $wisata) {
            $distance = $this->haversineDistance($userLat, $userLng, $wisata['latitude'], $wisata['longitude']);
            $distances[] = [
                'id' => $wisata['id'],
                'kode' => $wisata['kode'],
                'nama' => $wisata['nama'],
                'latitude' => $wisata['latitude'],
                'longitude' => $wisata['longitude'],
                'cluster' => $wisata['klaster'],
                'distance' => $distance
            ];
        }

        // Sort by distance
        usort($distances, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        return $this->response->setJSON([
            'success' => true,
            'data' => $distances
        ]);
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // in kilometers

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function getRecommendation()
    {
        $wisataModel = new \App\Models\WisataModel();
        $kriteriaPerengkinganModel = new \App\Models\KriteriaPerengkinganModel();
        $nilaiPerengkinganModel = new \App\Models\NilaiKriteriaPerengkinganModel();

        // Get all tourism data with ranking criteria values and distance
        $wisataData = $wisataModel->where('klaster IS NOT NULL')->findAll();
        $kriteriaPerengkingan = $kriteriaPerengkinganModel->findAll();

        // Prepare data for weighted product
        $rankingData = [];
        foreach ($wisataData as $wisata) {
            $criteriaValues = [];
            foreach ($kriteriaPerengkingan as $kriteria) {
                $nilai = $nilaiPerengkinganModel->where('wisata_kode', $wisata['kode'])
                    ->where('kriteria_perengkingan_kode', $kriteria['kode'])
                    ->first();
                $criteriaValues[$kriteria['kode']] = $nilai ? (float)$nilai['nilai'] : 0;
            }

            $rankingData[] = [
                'id' => $wisata['id'],
                'kode' => $wisata['kode'],
                'nama' => $wisata['nama'],
                'latitude' => $wisata['latitude'],
                'longitude' => $wisata['longitude'],
                'cluster' => $wisata['klaster'],
                'lokasi' => $criteriaValues['P001'] ?? 0,
                'fasilitas' => $criteriaValues['P002'] ?? 0,
                'biaya' => $criteriaValues['P003'] ?? 0,
                'keamanan' => $criteriaValues['P004'] ?? 0,
            ];
        }

        // Perform weighted product ranking
        $rankedData = $this->weightedProduct($rankingData);

        return $this->response->setJSON([
            'success' => true,
            'data' => $rankedData
        ]);
    }

    private function weightedProduct($data)
    {
        // Define weights for each criterion (adjust according to your needs)
        $weights = [
            'cluster' => 0.3,   // Higher weight for cluster (potential)
            'lokasi' => 0.2,
            'fasilitas' => 0.2,
            'biaya' => 0.1,      // Cost is a negative criterion (lower is better)
            'keamanan' => 0.2,
        ];

        // Calculate S for each alternative
        foreach ($data as &$item) {
            $s = 1;

            // For cluster (higher is better)
            $s *= pow($item['cluster'], $weights['cluster']);

            // For location (higher is better)
            $s *= pow($item['lokasi'], $weights['lokasi']);

            // For facilities (higher is better)
            $s *= pow($item['fasilitas'], $weights['fasilitas']);

            // For cost (lower is better, so we use inverse)
            $s *= pow(1 / max($item['biaya'], 0.01), $weights['biaya']);

            // For security (higher is better)
            $s *= pow($item['keamanan'], $weights['keamanan']);

            $item['s'] = $s;
        }

        // Sort by S value (descending)
        usort($data, function ($a, $b) {
            return $b['s'] <=> $a['s'];
        });

        // Assign ranks
        foreach ($data as $index => &$item) {
            $item['rank'] = $index + 1;
        }

        return $data;
    }
}
