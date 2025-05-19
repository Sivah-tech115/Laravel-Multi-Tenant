<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use ZipArchive;
use League\Csv\Reader;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Merchant;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImportAwinFeedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $zipUrl;
    protected $importId;

    public function __construct($zipUrl, $importId)
    {
        $this->zipUrl = $zipUrl;
        $this->importId = $importId;
    }

    public function handle()
    {
        $import = \App\Models\Import::find($this->importId);
        try {
            $import->update(['status' => 'processing']);
            // Download ZIP file
            $zipContents = file_get_contents($this->zipUrl);
            $zipFileName = 'awinfeed_' . time() . '.zip';
            Storage::disk('local')->put($zipFileName, $zipContents);

            $zipPath = storage_path('app/' . $zipFileName);

            // Extract ZIP
            $zip = new ZipArchive;
            if ($zip->open($zipPath) === TRUE) {
                $extractFolder = storage_path('app/awinfeed_extract_' . time());
                mkdir($extractFolder, 0755, true);
                $zip->extractTo($extractFolder);
                $zip->close();
            } else {
                Log::error('Failed to open ZIP file.');
                return;
            }

            // Find CSV file in extracted folder
            $csvFiles = glob($extractFolder . '/*.csv');
            if (empty($csvFiles)) {
                Log::error('No CSV file found inside ZIP.');
                return;
            }
            $csvPath = $csvFiles[0];

            // Parse CSV
            $csv = Reader::createFromPath($csvPath, 'r');
            $csv->setDelimiter(';');
            $csv->setHeaderOffset(0);
            $records = $csv->getRecords();

            $importCount = 0;

            foreach ($records as $record) {
                // Save or get brand
                $brand = Brand::firstOrCreate(
                    ['brand_id' => $record['brand_id']],
                    ['brand_name' => $record['brand_name'] ?? null]
                );

                // Save or get category
                $category = Category::firstOrCreate(
                    ['category_id' => $record['category_id']],
                    ['category_name' => $record['category_name'] ?? null]
                );

                // Save or get merchant
                $merchant = Merchant::firstOrCreate(
                    ['merchant_id' => $record['merchant_id']],
                    ['merchant_name' => $record['merchant_name'] ?? null]
                );

                // Save or update product
                Product::updateOrCreate(
                    ['aw_product_id' => $record['aw_product_id']],
                    [
                        'product_name' => $record['product_name'] ?? null,
                        'aw_deep_link' => $record['aw_deep_link'] ?? null,
                        'merchant_product_id' => $record['merchant_product_id'] ?? null,
                        'merchant_image_url' => $record['merchant_image_url'] ?? null,
                        'description' => $record['description'] ?? null,
                        'merchant_category' => $record['merchant_category'] ?? null,
                        'search_price' => $record['search_price'] ?? null,
                        'merchant_id' => $merchant->id,
                        'category_id' => $category->id,
                        'brand_id' => $brand->id,
                    ]
                );

                $importCount++;
            }

            // Cleanup files
            unlink($zipPath);
            // Optionally: recursive delete $extractFolder here

            Log::info("Awin feed import completed: {$importCount} products processed.");

            $import->update(['status' => 'completed']);


        } catch (\Exception $e) {
            Log::error('ImportAwinFeedJob error: ' . $e->getMessage());
            $import->update([
                'status' => 'failed',
                'log' => $e->getMessage(),
            ]);
        }
    }
}
