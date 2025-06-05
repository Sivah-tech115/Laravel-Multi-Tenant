<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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

class ProcessPendingImports extends Command
{
    protected $signature = 'app:process-pending-imports';
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
                $pendingImports = Import::where('status', 'pending')->get();

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
                    $this->processAwinImport($import);
                }
            });
        }

        return 0;
    }

    protected function processAwinImport($import)
    {
        $this->info("Starting Awin import for import ID {$import->id}");

        try {

            $import->update(['status' => 'processing']);

            $zipUrl = $import->zip_url;

            if (empty($zipUrl)) {
                Log::error("Missing zip URL for import ID: {$import->id}");
                $import->update([
                    'status' => 'failed',
                    'log' => 'Missing zip URL',
                ]);
                return;
            }

            $this->info("Downloading ZIP file...");

            $zipContents = file_get_contents($zipUrl);
            $zipFileName = 'awinfeed_' . time() . '.zip';
            Storage::disk('local')->put($zipFileName, $zipContents);
            $zipPath = storage_path('app/' . $zipFileName);

            $this->info("Extracting ZIP file...");
            $zip = new ZipArchive;
            if ($zip->open($zipPath) === TRUE) {
                $extractFolder = storage_path('app/awinfeed_extract_' . time());
                mkdir($extractFolder, 0755, true);
                $zip->extractTo($extractFolder);
                $zip->close();
            } else {
                $import->update(['status' => 'failed', 'log' => 'Failed to open ZIP file.']);
                return;
            }


            // $extractFolder = storage_path('app/awinfeed_extract_1749016847');

            $csvFiles = glob($extractFolder . '/*.csv');
            if (empty($csvFiles)) {
                $import->update(['status' => 'failed', 'log' => 'No CSV file found inside ZIP.']);
                return;
            }

            $csvPath = $csvFiles[0];
            $csv = Reader::createFromPath($csvPath, 'r');
            $csv->setDelimiter(';');
            $csv->setHeaderOffset(0);

            $offset = 0;
            $batchSize = 300;
            $importCount = 0;

            $this->info("Starting CSV import in chunks of {$batchSize}...");

            do {
                $stmt = (new Statement())->offset($offset)->limit($batchSize);
                $records = iterator_to_array($stmt->process($csv));

                if (empty($records)) {
                    break;
                }

                $productBatch = [];

                foreach ($records as $record) {
                    if (empty($record['aw_product_id'])) {
                        continue;
                    }

                    try {
                        // Initialize merchant, category, and brand as null
                        $merchant = $category = $brand = null;

                        if (!empty($record['brand_id'])) {
                            $brandName = $record['brand_name'] ?? 'unknown-brand';
                            $brand = Brand::updateOrCreate(
                                ['brand_id' => $record['brand_id']],
                                [
                                    'brand_name' => $brandName,
                                    'import_id' => $import->id ?? null,
                                    'slug' => Str::slug($brandName),
                                    'meta_title' => $brandName,
                                    'keyword' => $brandName,
                                    'status' => $import->reimport_status == 1 ? '1' : '0',
                                ]
                            );
                        }

                        if (!empty($record['category_id'])) {
                            $categoryName = $record['category_name'] ?? 'unknown-category';
                            $category = Category::updateOrCreate(
                                ['category_id' => $record['category_id']],
                                [
                                    'category_name' => $categoryName,
                                    'import_id' => $import->id ?? null,
                                    'slug' => Str::slug($categoryName),
                                    'meta_title' => $categoryName,
                                    'keyword' => $categoryName,
                                    'status' => $import->reimport_status == 1 ? '1' : '0',
                                ]
                            );
                        }

                        if (!empty($record['merchant_id'])) {
                            $merchantName = $record['merchant_name'] ?? 'unknown-merchant';
                            $merchant = Merchant::updateOrCreate(
                                ['merchant_id' => $record['merchant_id']],
                                [
                                    'merchant_name' => $merchantName,
                                    'import_id' => $import->id ?? null,
                                    'slug' => Str::slug($merchantName),
                                    'meta_title' => $merchantName,
                                    'keyword' => $merchantName,
                                    'status' => $import->reimport_status == 1 ? '1' : '0',
                                ]
                            );


                            $categoryString = $record['merchant_category'] ?? null;

                            if ($categoryString) {
                                if (strpos($categoryString, '>') !== false) {
                                    // Case 1: Category string with '>' separator (hierarchy)
                                    $categories = array_map('trim', explode('>', $categoryString));

                                    // Remove first category dynamically
                                    array_shift($categories);

                                    $parentCategory = null;

                                    foreach ($categories as $categoryName) {
                                        $subcategory = Subcategory::updateOrCreate(
                                            [
                                                'name' => $categoryName,
                                                'merchant_id' => $record['merchant_id'],
                                                'parent_id' => $parentCategory ? $parentCategory->id : null,
                                            ],
                                            [
                                                'import_id' => $import->id ?? null,
                                                'slug' => Str::slug($categoryName),
                                                'meta_title' => $categoryName,
                                                'keyword' => $categoryName,
                                                'status' => $import->reimport_status == 1 ? '1' : '0',
                                            ]
                                        );

                                        $parentCategory = $subcategory;
                                    }
                                } else {
                                    // Case 2: Single category (no '>')
                                    $categoryName = trim($categoryString);

                                    Subcategory::updateOrCreate(
                                        [
                                            'name' => $categoryName,
                                            'merchant_id' => $record['merchant_id'],
                                            'parent_id' => null,  // no parent because single category
                                        ],
                                        [
                                            'import_id' => $import->id ?? null,
                                            'slug' => Str::slug($categoryName),
                                            'meta_title' => $categoryName,
                                            'keyword' => $categoryName,
                                            'status' => $import->reimport_status == 1 ? '1' : '0',
                                        ]
                                    );
                                }
                            }
                        }



                        if (!empty($record['aw_product_id'])) {
                            // Prepare product batch

                            $productTitle = $record['product_name'] ?? 'untitled-product';
                            $productDescription = $record['description'] ?? null;
                            $shortTitle = Str::words($productTitle, 40, '');
                            $merchantName = $record['merchant_name'] ?? 'unknown-merchant';

                            $productBatch[] = [
                                'aw_product_id' => $record['aw_product_id'],
                                'import_id' => $import->id,
                                'slug' => Str::slug($productTitle),
                                'description' => $productDescription,
                                'meta_title' => $productTitle,
                                'meta_description' => $productDescription ? Str::words($productDescription, 30, '...') : null,
                                'keyword' => $shortTitle,
                                'product_name' => $record['product_name'] ?? null,
                                'aw_deep_link' => $record['aw_deep_link'] ?? null,
                                'merchant_product_id' => $record['merchant_product_id'] ?? null,
                                'merchant_image_url' => $record['merchant_image_url'] ?? null,
                                'description' => $record['description'] ?? null,
                                'merchant_category' => $record['merchant_category'] ?? null,
                                'merchant_category_slug' => str::slug($record['merchant_category']) ?? null,
                                'search_price' => $record['search_price'] ?? null,
                                'aw_image_url' => $record['aw_image_url'] ?? null,
                                'currency' => $record['currency'] ?? null,
                                'store_price' => $record['store_price'] ?? null,
                                'delivery_cost' => $record['delivery_cost'] ?? null,
                                'merchant_deep_link' => $record['merchant_deep_link'] ?? null,
                                'language' => $record['language'] ?? null,
                                'last_updated' => $record['last_updated'] ?? null,
                                'display_price' => $record['display_price'] ?? null,
                                'data_feed_id' => $record['data_feed_id'] ?? null,
                                'colour' => $record['colour'] ?? null,
                                'product_short_description' => $record['product_short_description'] ?? null,
                                'specifications' => $record['specifications'] ?? null,
                                'condition' => $record['condition'] ?? null,
                                'product_model' => $record['product_model'] ?? null,
                                'model_number' => $record['model_number'] ?? null,
                                'dimensions' => $record['dimensions'] ?? null,
                                'keywords' => $record['keywords'] ?? null,
                                'promotional_text' => $record['promotional_text'] ?? null,
                                'product_type' => $record['product_type'] ?? null,
                                'commission_group' => $record['commission_group'] ?? null,
                                'merchant_product_category_path' => $record['merchant_product_category_path'] ?? null,
                                'merchant_product_second_category' => $record['merchant_product_second_category'] ?? null,
                                'merchant_product_third_category' => $record['merchant_product_third_category'] ?? null,
                                'rrp_price' => $record['rrp_price'] ?? null,
                                'saving' => $record['saving'] ?? null,
                                'savings_percent' => $record['savings_percent'] ?? null,
                                'base_price' => $record['base_price'] ?? null,
                                'base_price_amount' => $record['base_price_amount'] ?? null,
                                'base_price_text' => $record['base_price_text'] ?? null,
                                'product_price_old' => $record['product_price_old'] ?? null,
                                'delivery_restrictions' => $record['delivery_restrictions'] ?? null,
                                'delivery_weight' => $record['delivery_weight'] ?? null,
                                'warranty' => $record['warranty'] ?? null,
                                'terms_of_contract' => $record['terms_of_contract'] ?? null,
                                'delivery_time' => $record['delivery_time'] ?? null,
                                'in_stock' => $record['in_stock'] ?? null,
                                'stock_quantity' => $record['stock_quantity'] ?? null,
                                'valid_from' => $record['valid_from'] ?? null,
                                'valid_to' => $record['valid_to'] ?? null,
                                'is_for_sale' => $record['is_for_sale'] ?? null,
                                'web_offer' => $record['web_offer'] ?? null,
                                'pre_order' => $record['pre_order'] ?? null,
                                'stock_status' => $record['stock_status'] ?? null,
                                'size_stock_status' => $record['size_stock_status'] ?? null,
                                'size_stock_amount' => $record['size_stock_amount'] ?? null,
                                'merchant_thumb_url' => $record['merchant_thumb_url'] ?? null,
                                'large_image' => $record['large_image'] ?? null,
                                'alternate_image' => $record['alternate_image'] ?? null,
                                'aw_thumb_url' => $record['aw_thumb_url'] ?? null,
                                'alternate_image_two' => $record['alternate_image_two'] ?? null,
                                'alternate_image_three' => $record['alternate_image_three'] ?? null,
                                'alternate_image_four' => $record['alternate_image_four'] ?? null,
                                'reviews' => $record['reviews'] ?? null,
                                'average_rating' => $record['average_rating'] ?? null,
                                'rating' => $record['rating'] ?? null,
                                'number_available' => $record['number_available'] ?? null,
                                'custom_1' => $record['custom_1'] ?? null,
                                'custom_2' => $record['custom_2'] ?? null,
                                'custom_3' => $record['custom_3'] ?? null,
                                'custom_4' => $record['custom_4'] ?? null,
                                'custom_5' => $record['custom_5'] ?? null,
                                'custom_6' => $record['custom_6'] ?? null,
                                'custom_7' => $record['custom_7'] ?? null,
                                'custom_8' => $record['custom_8'] ?? null,
                                'custom_9' => $record['custom_9'] ?? null,
                                'ean' => $record['ean'] ?? null,
                                'isbn' => $record['isbn'] ?? null,
                                'upc' => $record['upc'] ?? null,
                                'mpn' => $record['mpn'] ?? null,
                                'parent_product_id' => $record['parent_product_id'] ?? null,
                                'product_GTIN' => $record['product_GTIN'] ?? null,
                                'basket_link' => $record['basket_link'] ?? null,
                                'merchant_id' => isset($merchant) && isset($merchant->id) ? $merchant->id : null,
                                'category_id' => isset($category) && isset($category->id) ? $category->id : null,
                                'brand_id' => isset($brand) && isset($brand->id) ? $brand->id : null,
                                'status' => $import->reimport_status == 1 ? '1' : '0',
                            ];
                        }
                    } catch (\Exception $e) {
                        Log::error("Error processing record: " . $e->getMessage(), $record);
                        continue;
                    }
                }

                // Upsert product batch
                if (!empty($productBatch)) {
                    Product::upsert($productBatch, ['aw_product_id'], [
                        'slug',
                        'meta_title',
                        'meta_description',
                        'keyword',
                        'import_id',
                        'product_name',
                        'aw_deep_link',
                        'merchant_product_id',
                        'merchant_image_url',
                        'description',
                        'merchant_category',
                        'search_price',
                        'merchant_id',
                        'category_id',
                        'aw_image_url',
                        'currency',
                        'store_price',
                        'delivery_cost',
                        'merchant_deep_link',
                        'language',
                        'last_updated',
                        'display_price',
                        'data_feed_id',
                        'brand_id',
                        'colour',
                        'product_short_description',
                        'specifications',
                        'condition',
                        'product_model',
                        'model_number',
                        'dimensions',
                        'keywords',
                        'promotional_text',
                        'product_type',
                        'commission_group',
                        'merchant_product_category_path',
                        'merchant_product_second_category',
                        'merchant_product_third_category',
                        'rrp_price',
                        'saving',
                        'savings_percent',
                        'base_price',
                        'base_price_amount',
                        'base_price_text',
                        'product_price_old',
                        'delivery_restrictions',
                        'delivery_weight',
                        'warranty',
                        'terms_of_contract',
                        'delivery_time',
                        'in_stock',
                        'stock_quantity',
                        'valid_from',
                        'valid_to',
                        'is_for_sale',
                        'web_offer',
                        'pre_order',
                        'stock_status',
                        'size_stock_status',
                        'size_stock_amount',
                        'merchant_thumb_url',
                        'large_image',
                        'alternate_image',
                        'aw_thumb_url',
                        'alternate_image_two',
                        'alternate_image_three',
                        'alternate_image_four',
                        'reviews',
                        'average_rating',
                        'rating',
                        'number_available',
                        'custom_1',
                        'custom_2',
                        'custom_3',
                        'custom_4',
                        'custom_5',
                        'custom_6',
                        'custom_7',
                        'custom_8',
                        'custom_9',
                        'ean',
                        'isbn',
                        'upc',
                        'mpn',
                        'parent_product_id',
                        'product_GTIN',
                        'basket_link'
                    ]);
                    $importCount += count($productBatch);
                }

                $offset += $batchSize;

                //  Log::info("Process import ID: {$import->id}, Offset: {$offset}...");
                DB::table('imports')->where('id', $import->id)
                    ->update(['log' => 'Imported products: ' . $offset]);
            } while (count($records) > 0);

            if ($import->reimport_status == 1) {

                // Delete old unused records
                DB::table('products')
                    ->where('import_id', $import->id)
                    ->where('status', '0')
                    ->delete();

                DB::table('merchants')
                    ->where('import_id', $import->id)
                    ->where('status', '0')
                    ->delete();

                DB::table('categories')
                    ->where('import_id', $import->id)
                    ->where('status', '0')
                    ->delete();

                DB::table('brands')
                    ->where('import_id', $import->id)
                    ->where('status', '0')
                    ->delete();

                DB::table('products')->where('import_id', $import->id)->update(['status' => '0']);
                DB::table('merchants')->where('import_id', $import->id)->update(['status' => '0']);
                DB::table('categories')->where('import_id', $import->id)->update(['status' => '0']);
                DB::table('brands')->where('import_id', $import->id)->update(['status' => '0']);
            }

            unlink($zipPath);
            $this->deleteDirectory($extractFolder);

            $import->update([
                'status' => 'completed',
                'reimport_status' => '0',
                'log' => "Successfully imported {$importCount} products",
            ]);
        } catch (\Exception $e) {
            Log::error("processAwinImport error: " . $e->getMessage());
            $import->update([
                'status' => 'failed',
                'log' => $e->getMessage(),
            ]);
        }
    }


    /**
     * Recursively delete a directory
     */
    protected function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }
}
