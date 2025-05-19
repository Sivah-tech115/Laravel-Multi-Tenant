<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Jobs\SeedTenantLang;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;

class WebsiteController extends Controller
{

    public function index(Request $request)
    {
        SeedTenantLang::dispatchSync();
        
        $domain = tenant()->domains[0]['domain'] ?? 'en.localhost';
        $langCode = explode('.', $domain)[0] ?? 'en';
        $targetLocale = $langCode;
    
        // Translate if not 'en'
        if ($targetLocale !== 'en') {
            $baseTranslations = DB::table('translations')->where('locale', 'en')->get();
    
            foreach ($baseTranslations as $translation) {
                $exists = DB::table('translations')
                    ->where('locale', $targetLocale)
                    ->where('key', $translation->key)
                    ->exists();
    
                if (!$exists) {
                    try {
                        $translatedValue = (new GoogleTranslate($targetLocale))->translate($translation->value);
    
                        DB::table('translations')->insert([
                            'locale' => $targetLocale,
                            'key' => $translation->key,
                            'value' => $translatedValue,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } catch (\Exception $e) {
                        // Handle failure (optional)
                        logger()->error("Translation failed for {$translation->key}: " . $e->getMessage());
                    }
                }
            }
        }
    
        $products = Product::all();
    
        return view('app.website.index', compact('products'));
    }
    
}
