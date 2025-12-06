<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Promise\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PemasokController extends Controller
{
    public function index(Request $request)
    {
        // Validasi cabang aktif
        $activeBranchId = session('active_branch');
        if (!$activeBranchId) {
            return back()->with('error', 'Cabang belum dipilih.');
        }

        $branch = Branch::find($activeBranchId);
        if (!$branch) {
            return back()->with('error', 'Cabang tidak valid.');
        }

        // Validasi kredensial Accurate
        if (!$branch->accurate_api_token || !$branch->accurate_signature_secret) {
            return back()->with('error', 'Kredensial Accurate untuk cabang ini belum dikonfigurasi.');
        }

        // Log request parameters untuk debugging
        Log::info('PemasokController index called', [
            'force_refresh' => $request->has('force_refresh'),
            'force_refresh_value' => $request->get('force_refresh')
        ]);

        // Cache key yang unik
        $cacheKey = 'accurate_supplier_list_' . $activeBranchId;
        // Tetapkan waktu cache (dalam menit)
        $cacheDuration = 30; // 30 menit

        // Jika ada parameter force_refresh, bypass cache dan hapus cache lama
        if ($request->has('force_refresh')) {
            Cache::forget($cacheKey);
            Log::info('Cache pemasok dihapus karena force_refresh');
        }

        // --- TAMBAHAN: Inisialisasi variabel pesan error ---
        $errorMessage = null;

        // Periksa apakah cache sudah ada (hanya jika tidak ada force_refresh)
        if (Cache::has($cacheKey) && !$request->has('force_refresh')) {
            $cachedData = Cache::get($cacheKey);
            // Ambil data pemasok dan pesan error dari cache
            $pemasok = $cachedData['pemasok'] ?? [];
            $errorMessage = $cachedData['errorMessage'] ?? null;

            Log::info('Data pemasok diambil dari cache');
            // Kirim kedua variabel ke view
            return view('pemasok.index', compact('pemasok', 'errorMessage'));
        }

        // Get the auth and session tokens from the configuration
        $apiToken = $branch->accurate_api_token;
        $signatureSecret = $branch->accurate_signature_secret;
        $baseUrl = rtrim($branch->url_accurate ?? 'https://iris.accurate.id/accurate/api', '/');
        $timestamp = Carbon::now()->toIso8601String();
        $signature = hash_hmac('sha256', $timestamp, $signatureSecret);

        // Define the API URL and query parameters
        $apiUrl = $baseUrl . '/vendor/list.do';
        $data = [
            'fields' => 'name,billStreet,wpName,vendorNo,balance,id',
            'sp.page' => 1,
            'sp.pageSize' => 20
        ];

        // Initialize variables
        $pemasok = [];
        $allPemasok = [];
        $apiSuccess = false;

        try {
            // Selalu coba ambil data dari API terlebih dahulu
            $firstPageResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiToken,
                'X-Api-Signature' => $signature,
                'X-Api-Timestamp' => $timestamp,
            ])->get($apiUrl, $data);

            // Log the response for debugging
            Log::info('API Response:', [
                'status' => $firstPageResponse->status(),
                'body' => $firstPageResponse->body(),
            ]);

            // Check if the response is successful
            if ($firstPageResponse->successful()) {
                $responseData = $firstPageResponse->json();

                // Logging data list supplier mentah dari Accurate
                Log::info('Accurate Supplier list first page response:', $responseData);

                if (isset($responseData['d']) && is_array($responseData['d'])) {
                    $allPemasok = $responseData['d'];

                    // Hitung total halaman berdasarkan sp.rowCount jika tersedia
                    $totalItems = $responseData['sp']['rowCount'] ?? 0;
                    $totalPages = ceil($totalItems / 20); // 20 adalah pageSize

                    // Jika lebih dari 1 halaman, ambil halaman lainnya secara paralel
                    if ($totalPages > 1) {
                        // Buat array untuk menyimpan semua promise
                        $promises = [];
                        $client = new \GuzzleHttp\Client();

                        // Buat promise untuk setiap halaman (mulai dari halaman 2)
                        for ($page = 2; $page <= $totalPages; $page++) {
                            $promises[$page] = $client->getAsync($apiUrl, [
                                'headers' => [
                                    'Authorization' => 'Bearer ' . $apiToken,
                                    'X-Api-Signature' => $signature,
                                    'X-Api-Timestamp' => $timestamp,
                                ],
                                'query' => [
                                    'fields' => 'name,billStreet,wpName,vendorNo,balance,id',
                                    'sp.page' => $page,
                                    'sp.pageSize' => 20
                                ]
                            ]);
                        }

                        // Jalankan semua promise secara paralel
                        $results = Utils::settle($promises)->wait();

                        // Proses hasil dari setiap promise
                        foreach ($results as $page => $result) {
                            if ($result['state'] === 'fulfilled') {
                                $pageResponse = json_decode($result['value']->getBody(), true);
                                if (isset($pageResponse['d']) && is_array($pageResponse['d'])) {
                                    // Gabungkan data dari halaman ini
                                    $allPemasok = array_merge($allPemasok, $pageResponse['d']);
                                    Log::info("Accurate Supplier list page {$page} response processed");
                                }
                            } else {
                                Log::error("Failed to fetch page {$page}: " . $result['reason']);
                            }
                        }
                    }

                    // Setelah mendapatkan semua ID pemasok, ambil detail untuk masing-masing secara batch
                    $detailsResult = $this->fetchPemasokDetailsInBatches($allPemasok, $apiToken, $signature, $timestamp, $baseUrl);
                    $pemasok = $detailsResult['details']; // Ambil data pemasok
                    $apiSuccess = true;

                    // --- TAMBAHAN: Jika ada error, siapkan pesan ---
                    if ($detailsResult['has_error']) {
                        $errorMessage = 'Gagal memuat semua data pemasok karena terlalu banyak permintaan ke server. Data yang ditampilkan mungkin tidak lengkap. Silakan coba lagi dengan menekan tombol "Refresh Data".';
                        Log::warning($errorMessage);
                    }

                    // Simpan data ke cache setelah berhasil mendapatkan data dari API
                    if (!empty($pemasok)) {
                        Cache::put($cacheKey, ['pemasok' => $pemasok, 'errorMessage' => $errorMessage], $cacheDuration * 60);
                        Log::info('Data pemasok berhasil diambil dari API dan disimpan ke cache', [
                            'count' => count($pemasok),
                            'cache_duration' => $cacheDuration . ' minutes'
                        ]);
                    } else {
                        Log::warning('Data pemasok kosong, tidak disimpan ke cache');
                    }
                }
            } else {
                Log::error('Gagal mengambil daftar pemasok dari Accurate.', [
                    'response' => $firstPageResponse->body()
                ]);
            }
        } catch (Exception $e) {
            Log::error('Exception saat mengambil data dari Accurate API: ' . $e->getMessage());
        }

        // Jika API gagal atau tidak ada data, gunakan cache sebagai fallback
        if (!$apiSuccess || empty($pemasok)) {
            if (Cache::has($cacheKey)) {
                $cachedData = Cache::get($cacheKey);
                $pemasok = $cachedData['pemasok'] ?? [];
                // Jangan timpa pesan error jika API gagal total
                if (is_null($errorMessage)) {
                    $errorMessage = $cachedData['errorMessage'] ?? null;
                }
            } else {
                Log::warning('API Accurate gagal dan tidak ada data cache tersedia');
                $pemasok = [];
                $errorMessage = 'Gagal terhubung ke server Accurate dan tidak ada data cache. Silakan coba beberapa saat lagi.';
            }
        }

        // Jika parameter force_refresh ada dan API berhasil, log bahwa refresh berhasil
        if ($request->has('force_refresh') && $apiSuccess) {
            Log::info('Cache pemasok berhasil diperbarui dengan data terbaru dari API');
        }

        return view('pemasok.index', compact('pemasok', 'errorMessage'));
    }

    /**
     * Mengambil detail pemasok dalam batch untuk mengoptimalkan performa
     */
    private function fetchPemasokDetailsInBatches($pemasok, $apiToken, $signature, $timestamp, string $baseUrl, $batchSize = 5)
    {
        $pemasokDetails = [];
        $batches = array_chunk($pemasok, $batchSize);

        // --- TAMBAHAN: Inisialisasi flag error ---
        $hasApiError = false;

        foreach ($batches as $batch) {
            $promises = [];
            $client = new \GuzzleHttp\Client();

            foreach ($batch as $pemasok) {
                $detailUrl = $baseUrl . '/vendor/detail.do?id=' . $pemasok['id'];
                $promises[$pemasok['id']] = $client->getAsync($detailUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $apiToken,
                        'X-Api-Signature' => $signature,
                        'X-Api-Timestamp' => $timestamp,
                    ]
                ]);
            }

            // Jalankan batch promise secara paralel
            $results = Utils::settle($promises)->wait();

            // Proses hasil dari setiap promise
            foreach ($results as $pemasokId => $result) {
                if ($result['state'] === 'fulfilled') {
                    $detailResponse = json_decode($result['value']->getBody(), true);
                    if (isset($detailResponse['d'])) {
                        $pemasokDetails[$pemasokId] = $detailResponse['d'];
                        Log::info("Pemasok detail fetched for ID: {$pemasokId}");
                    }
                } else {
                    // --- MODIFIKASI: Deteksi error spesifik ---
                    $reason = $result['reason'];
                    Log::error("Failed to fetch pemasok detail for ID {$pemasokId}: " . $reason->getMessage());

                    // Cek jika error adalah ClientException dan status code adalah 429
                    if ($reason instanceof ClientException && $reason->getResponse()->getStatusCode() == 429) {
                        $hasApiError = true; // Set flag error menjadi true
                    }
                }
            }

            // Tambahkan delay kecil antara batch untuk menghindari rate limiting
            usleep(200000); // 200ms
        }

        return [
            'details' => $pemasokDetails,
            'has_error' => $hasApiError
        ];
    }
}
