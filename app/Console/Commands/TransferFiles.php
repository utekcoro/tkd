<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Command\Command as ConsoleCommand;

class TransferFiles extends Command
{
    protected $signature = 'files:transfer';
    protected $description = 'Tarik file dari AWS dan kirim ke VPS Taka';

    public function handle()
    {
        // Mengakses disk SFTP sumber (AWS)
        $sourceDisk = Storage::disk('sftp_source');
        $targetDisk = Storage::disk('sftp_target');

        // Daftar file yang ingin diambil
        $filesToTransfer = [
            'EXPORT_BARCODE_TAKA.txt',
        ];

        $transferCount = 0;

        foreach ($filesToTransfer as $fileName) {
            $this->info("Checking file: " . $fileName);
            
            // Cek apakah file ada di source
            if ($sourceDisk->exists($fileName)) {
                try {
                    $fileContent = $sourceDisk->get($fileName);
                    $targetDisk->put($fileName, $fileContent);

                    $this->info("✓ File '$fileName' berhasil dipindahkan ke VPS.");
                    $transferCount++;
                } catch (\Exception $e) {
                    $this->error("✗ Gagal memindahkan file '$fileName': " . $e->getMessage());
                }
            } else {
                $this->warn("File '$fileName' tidak ditemukan di source.");
            }
        }

        if ($transferCount === 0) {
            $this->info("Tidak ada file yang perlu ditransfer.");
        } else {
            $this->info("Total $transferCount file berhasil ditransfer.");
        }

        return ConsoleCommand::SUCCESS;
    }
}