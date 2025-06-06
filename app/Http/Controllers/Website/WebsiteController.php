<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Merchant;
use App\Models\Brand;
use App\Models\Category;
use App\Jobs\SeedTenantLang;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Cache;


class WebsiteController extends Controller
{

    protected $langCode;
    protected $targetLocale;

    public function __construct()
    {
        $domain = tenant()->domains[0]['domain'] ?? 'en.localhost';
        $this->langCode = explode('.', $domain)[0] ?? 'en';
        $this->targetLocale = $this->langCode;
    }


    public function index(Request $request)
    {
        $sorting = $request->input('sorting', 'popularity');
        $query = $request->input('query');
        $merchantquery = $request->query('merchant');
        $page = $request->input('page', 1);
        SeedTenantLang::dispatchSync();

        if ($this->targetLocale !== 'en') {
            $baseTranslations = DB::table('translations')->where('locale', 'en')->get();

            foreach ($baseTranslations as $translation) {
                $exists = DB::table('translations')
                    ->where('locale', $this->targetLocale)
                    ->where('key', $translation->key)
                    ->exists();

                if (!$exists) {
                    try {
                        $translatedValue = (new GoogleTranslate($this->targetLocale))->translate($translation->value);

                        DB::table('translations')->insert([
                            'locale' => $this->targetLocale,
                            'key' => $translation->key,
                            'value' => $translatedValue,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } catch (\Exception $e) {
                        logger()->error("Translation failed for {$translation->key}: " . $e->getMessage());
                    }
                }
            }
        }

        $sessionKey = "products{$this->targetLocale}_{$sorting}_{$query}_{$merchantquery}_page_{$page}";

        if (session()->has($sessionKey)) {
            $products = session()->get($sessionKey);
        } else {
            $queryBuilder = Product::query();
            if ($merchantquery) {
                $merchant = Merchant::where('slug', $merchantquery)->first();
                if ($merchant) {
                    $queryBuilder->where('merchant_id', $merchant->id);
                }
            }

            if ($query) {
                $queryBuilder->where(function ($q) use ($query) {
                    $q->where('product_name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                });
            }

            switch ($sorting) {
                case 'rating':
                    $queryBuilder->orderByRaw('CAST(rating AS UNSIGNED) desc');
                    break;
                case 'newest':
                    $queryBuilder->orderBy('created_at', 'desc');
                    break;
                case 'lowest_price':
                    $queryBuilder->orderByRaw('CAST(search_price AS DECIMAL(10,2)) ASC');
                    break;
                case 'highest_price':
                    $queryBuilder->orderByRaw('CAST(search_price AS DECIMAL(10,2)) DESC');
                    break;
                case 'popularity':
                default:
                    $queryBuilder->orderBy('search_price', 'desc');
                    break;
            }

            $products = $queryBuilder->paginate(12);

            session()->put($sessionKey, $products);
        }

        // Caching the merchants list
        $merchantsSessionKey = "index_merchants_list{$this->targetLocale}";

        $merchants = session()->get($merchantsSessionKey);

        if (!$merchants) {
            // $merchants = Merchant::with('subcategory')->get();
            $merchants = Merchant::with(['subcategories'])->get(); // just load subcategories without parent_id check

            // Then load children only if subcategories have children relation and children exist
            $merchants->each(function ($merchant) {
                $merchant->subcategories->each(function ($subcategory) {
                    // Check if children relation exists and has any children, then load
                    if (method_exists($subcategory, 'children') && $subcategory->children()->exists()) {
                        $subcategory->load('children');
                    }
                });
            });

            session()->put($merchantsSessionKey, $merchants);
        }

        // dd($merchants->toArray());

        if ($query) {
            return view('app.website.pages.search-page', compact('products', 'merchantquery', 'merchants'));
        }

        return view('app.website.pages.index', compact('products', 'merchantquery', 'merchants'));
    }


    public function categoryProducts(Request $request, $categorySlug, $subcategorySlug2 = null, $subcategorySlug3 = null)
    {
        $sorting = $request->input('sorting', 'popularity');
 
        $categorySessionKey = "merchant_{$this->langCode}_{$categorySlug}";
        $category = session()->get($categorySessionKey);


        if (!$category) {
            $category = Merchant::where('slug', $categorySlug)->firstOrFail();
            session()->put($categorySessionKey, $category);
        }

        $categoryId = $category->id;
        $page = $request->get('page', 1);
        $subcategory = null;
        $subSubcategory = null;

        // If subcategory (level 2) is provided
        if ($subcategorySlug2) {
            $subcategorySessionKey = "subcategory_{$this->langCode}_{$categoryId}_{$subcategorySlug2}";
            $subcategory = session()->get($subcategorySessionKey);

            if (!$subcategory) {
                $subcategory = $category->subcategories()
                    ->where('slug', $subcategorySlug2)
                    ->firstOrFail();
                session()->put($subcategorySessionKey, $subcategory);
            }

            // If subcategory (level 3) is provided
            if ($subcategorySlug3) {
                $subSubcategorySessionKey = "subcategory3_{$this->langCode}_{$categoryId}_{$subcategorySlug3}";
                $subSubcategory = session()->get($subSubcategorySessionKey);

                if (!$subSubcategory) {
                    $subSubcategory = $subcategory->children()
                        ->where('slug', $subcategorySlug3)
                        ->firstOrFail();
                    session()->put($subSubcategorySessionKey, $subSubcategory);
                }
            }
        }

        // Caching products list
        $subcategorySlug2Key = $subcategorySlug2 ?? 'none';
        $subcategorySlug3Key = $subcategorySlug3 ?? 'none';

        $productsSessionKey = "category_products_{$this->langCode}_{$categoryId}_{$subcategorySlug2Key}_{$subcategorySlug3Key}_sorting_{$sorting}_page_{$page}";

        //    dd($productsSessionKey);
        $products = session()->get($productsSessionKey);

        // session()->forget($productsSessionKey);


        if (!$products) {
            $query = Product::where('merchant_id', $categoryId);

            // Filter by subcategory slug (level 2 or 3)
            if ($subcategorySlug3) {
                $query->where('merchant_category_slug', 'LIKE', "%{$subcategorySlug3}%");
            } elseif ($subcategorySlug2) {
                $query->where('merchant_category_slug', 'LIKE', "%{$subcategorySlug2}%");
            }

            // Sorting logic
            switch ($sorting) {
                case 'rating':
                    $query->orderByRaw('CAST(rating AS UNSIGNED) desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'lowest_price':
                    $query->orderByRaw('CAST(search_price AS DECIMAL(10,2)) ASC');
                    break;
                case 'highest_price':
                    $query->orderByRaw('CAST(search_price AS DECIMAL(10,2)) DESC');
                    break;
                case 'popularity':
                default:
                    $query->orderBy('search_price', 'desc');
                    break;
            }

            $products = $query->paginate(12);
            session()->put($productsSessionKey, $products);
        }

        // Caching the merchants list
        $merchantsSessionKey = 'category_merchants_list';
        $merchants = session()->get($merchantsSessionKey);

        if (!$merchants) {
            $merchants = Merchant::with(['subcategories' => function ($query) {
                $query->whereNull('parent_id')->with('children');
            }])->get();
            session()->put($merchantsSessionKey, $merchants);
        }

        $merchantquery = $category->merchant_name;
        $categoryquery = $subcategory?->name ?? null;
        $subsubcategoryquery = $subSubcategory?->name ?? null;


        return view('app.website.pages.category-product', compact('products', 'merchantquery', 'categoryquery', 'subsubcategoryquery', 'merchants'));
    }



    public function singleProduct(Request $request, $productSlug)
    {
        $sessionKey = "product_{$this->langCode}_{$productSlug}";
        $product = session()->get($sessionKey);

        if (!$product) {
            $product = Product::where('slug', $productSlug)->firstOrFail();
            session()->put($sessionKey, $product);
        }

        $merchantId = $product->merchant_id;
        $categorySlug = $product->merchant_category_slug;

        $relatedProducts = Product::where('merchant_id', $merchantId)
            ->where('slug', '!=', $productSlug)
            ->orWhere('merchant_category_slug', $categorySlug)
            ->with('brand')
            ->take(10)
            ->get();

        return view('app.website.pages.single-product', compact('product', 'relatedProducts'));
    }

    public function AllCategories(Request $request)
    {
        $merchants = Merchant::paginate(2);
        return view('app.website.pages.categories', compact('merchants'));
    }






}
