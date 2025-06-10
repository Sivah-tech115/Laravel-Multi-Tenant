<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    protected $signature = 'app:generate-sitemap';
    protected $description = 'Generate a sitemap for the application';

    public function handle()
    {
        // Get all tenants
        $tenants = \App\Models\Tenant::all();

        foreach ($tenants as $tenant) {
            $tenant->run(function () use ($tenant) {
                $this->info("Now in tenant database context");

                $this->info("📦 Generating sitemaps...");

                // Create the base sitemap directory under public
                $sitemapBaseDirectory = public_path('sitemaps');
                if (!file_exists($sitemapBaseDirectory)) {
                    $this->info("📂 Creating base directory at: {$sitemapBaseDirectory}");
                    if (!mkdir($sitemapBaseDirectory, 0775, true)) {
                        $this->error("❌ Failed to create directory at: {$sitemapBaseDirectory}");
                        return;
                    }
                } else {
                    $this->info("📂 Base directory already exists: {$sitemapBaseDirectory}");
                }

                // Models to generate sitemaps for
                $models = [
                    'products' => [Product::class, 'single.product', 'slug']
                ];

                // Create a folder for each tenant in public/sitemaps
                $tenantSitemapDirectory = $sitemapBaseDirectory . '/sitemap_' . $tenant->id;

                if (!file_exists($tenantSitemapDirectory)) {
                    $this->info("📂 Creating tenant directory at: {$tenantSitemapDirectory}");
                    if (!mkdir($tenantSitemapDirectory, 0775, true)) {
                        $this->error("❌ Failed to create tenant directory at: {$tenantSitemapDirectory}");
                        return;
                    }
                }

                // Clean up old sitemap files (if necessary)
                $this->info("🧹 Cleaning up old sitemap files...");
                $existingFiles = glob($tenantSitemapDirectory . "/sitemap-*.xml");
                foreach ($existingFiles as $file) {
                    File::delete($file);
                }

                // Path to the sitemap index file
                $indexFilePath = $tenantSitemapDirectory . '/sitemap_index.xml';

                // Check if the index file exists
                if (file_exists($indexFilePath)) {
                    $this->info("📂 Sitemap index file exists. Appending to it...");
                    // Load the existing sitemap index XML file
                    $existingIndexContent = file_get_contents($indexFilePath);
                    $existingIndex = simplexml_load_string($existingIndexContent);

                    // If the existing index doesn't contain a <sitemap> node, create one
                    if (!$existingIndex) {
                        $existingIndex = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>');
                    }
                } else {
                    $this->info("📂 Creating new sitemap index file...");
                    // If the index file doesn't exist, create a new XML structure
                    $existingIndex = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>');
                }

                foreach ($models as $key => [$modelClass, $routeName, $slugField]) {
                    $this->info("🔄 Checking {$key}...");

                    // Check if sitemap files exist and were modified today
                    $existingFiles = glob($tenantSitemapDirectory . "/sitemap-{$key}-*.xml");
                    $skipGeneration = false;

                    foreach ($existingFiles as $file) {
                        if (date('Y-m-d', filemtime($file)) === date('Y-m-d')) {
                            $skipGeneration = true;
                            break;
                        }
                    }

                    if ($skipGeneration) {
                        $this->info("⏩ Skipping {$key} sitemap generation — files already generated today.");
                        // Add existing sitemap files to index
                        foreach ($existingFiles as $file) {
                            $filename = basename($file);
                            // Append the new link to the existing sitemap index
                            $sitemap = $existingIndex->addChild('sitemap');
                            $sitemap->addChild('loc', url("sitemaps/sitemap_{$tenant->id}/{$filename}"));
                            $sitemap->addChild('lastmod', date(DATE_ATOM, filemtime($file)));
                        }
                        continue;
                    }

                    $this->info("🔄 Generating sitemap for {$key}...");

                    $chunkCount = 0;

                    $modelClass::whereNotNull($slugField)->chunk(10000, function ($items) use (&$chunkCount, $key, $routeName, $slugField, $tenantSitemapDirectory, $tenant, $existingIndex) {
                        $chunkCount++;
                        $sitemapFileName = "sitemap-{$key}-{$chunkCount}.xml";
                        $sitemapFilePath = $tenantSitemapDirectory . '/' . $sitemapFileName;

                        $sitemap = Sitemap::create();

                        foreach ($items as $item) {
                            try {
                                $url = route($routeName, [$slugField => $item->{$slugField}]);

                                $sitemap->add(
                                    Url::create($url)
                                        ->setLastModificationDate($item->updated_at ?? now())
                                        ->setPriority(0.8)
                                );
                            } catch (\Throwable $e) {
                                $this->warn("❌ Skipping {$key} ID {$item->id} - " . $e->getMessage());
                                continue; // Skips this item and continues
                            }
                        }

                        // Write the sitemap to the tenant's directory
                        $sitemap->writeToFile($sitemapFilePath);

                        // Append the newly generated sitemap file to the existing index
                        $sitemapIndexEntry = $existingIndex->addChild('sitemap');
                        $sitemapIndexEntry->addChild('loc', url("sitemaps/sitemap_{$tenant->id}/{$sitemapFileName}"));
                        $sitemapIndexEntry->addChild('lastmod', now()->toAtomString());
                    });

                    $this->info("✅ Sitemap for {$key} generated with {$chunkCount} file(s).");
                }

                // Write the updated sitemap index to the tenant's directory (appending the new sitemap entries)
                $existingIndex->asXML($indexFilePath);

                $this->info("🎉 All sitemaps generated successfully for tenant {$tenant->id}");
            });
        }

        return 0;
    }
}
