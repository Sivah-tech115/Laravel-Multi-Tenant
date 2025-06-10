<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BulkFeed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use ZipArchive;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Import;
use League\Csv\Statement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class BulkFeedimport extends Command
{
    protected $signature = 'app:process-pending-bulk-imports';
    protected $description = 'Process pending imports for all tenants';

    public function handle()
    {
        // Get all tenants
        $tenants = \App\Models\Tenant::all();

        foreach ($tenants as $tenant) {
            // $this->info("Running pending imports for tenant: {$tenant->id}");

            // Use the tenant->run() method which is the recommended approach
            $tenant->run(function () use ($tenant) {
                $this->info("Now in tenant database context");

                // Get pending imports for this tenant
                $pendingImports = BulkFeed::where('status', 'pending')->get();

                foreach ($pendingImports as $import) {
                    $import->update([
                        'status' => 'processing',
                        'log' => 'processing this file',
                    ]);
                }

                if ($pendingImports->isEmpty()) {
                    $this->info("No pending imports found for tenant {$tenant->id}");
                    return;
                }

                // $this->info("Found " . $pendingImports->count() . " pending imports to process");

                foreach ($pendingImports as $import) {
                    $this->processAwinFeedImport($import);
                }
            });
        }

        return 0;
    }

    protected function processAwinFeedImport($import)
    {
        $this->info("Starting Awin import for import ID {$import->id}");
    
        try {
            $import->update(['status' => 'processing']);
    
            $Url = $import->zip_url;
    
            if (empty($Url)) {
                Log::error("Missing URL for import ID: {$import->id}");
                $import->update([
                    'status' => 'failed',
                    'log' => 'Missing URL',
                ]);
                return;
            }
    
            $this->info("Downloading file...");
    
            // Download file content
            $fileContents = file_get_contents($Url);
            if ($fileContents === false) {
                throw new \Exception("Failed to download file from URL: $Url");
            }
    
            // Define folder path in public/bulkfeed
            $publicFolder = public_path('bulkfeed');
            if (!File::exists($publicFolder)) {
                File::makeDirectory($publicFolder, 0755, true);
            }
    
            // Create unique file name
            $fileName = 'Bulk_awinfeed_' . time() . '.csv';
            $filePath = $publicFolder . '/' . $fileName;
    
            // Save the file
            file_put_contents($filePath, $fileContents);
    
            // Verify file exists
            if (!File::exists($filePath)) {
                Log::error("Failed to save the file to public path for import ID: {$import->id}");
                $import->update([
                    'status' => 'failed',
                    'log' => 'Failed to save file to public path.',
                ]);
                return;
            }
    
            $this->info("File downloaded and saved to: " . $filePath);
    
            // Generate a public URL
            $publicUrl = url('bulkfeed/' . $fileName);
    
            // Update import record (ensure you have a `local_file_url` column or change accordingly)
            $import->update([
                'status' => 'completed',
                'log' => 'File saved successfully.',
                'local_file_url' => $publicUrl,
            ]);
    
            $this->info("Public file URL: " . $publicUrl);
    
        } catch (\Exception $e) {
            Log::error("Error processing import ID {$import->id}: " . $e->getMessage());
            $import->update([
                'status' => 'failed',
                'log' => 'Error processing import: ' . $e->getMessage(),
            ]);
        }
    }
    
}
