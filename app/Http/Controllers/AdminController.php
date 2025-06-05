<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Merchant;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class AdminController extends Controller
{
    public function showMerchant()
    {
        $merchant = Merchant::get();
        return view('app.adminPanel.merchant.index', compact('merchant'));
    }

    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image',
        ]);

        $merchant = Merchant::where('merchant_id', $id)->first();

        if (!$merchant) {
            return redirect()->back()->with('error', 'Merchant not found.');
        }

        if ($request->hasFile('image')) {
            $tenantId = tenant()->id;

            $directory = public_path("tenant_$tenantId/merchant_images");

            if (!file_exists($directory)) {
                mkdir($directory, 0775, true);
            }

            $image = $request->file('image');
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();

            $image->move($directory, $imageName);

            $merchant->image_url = "tenant_$tenantId/merchant_images/$imageName";
            $merchant->save();
        }

        return redirect()->back()->with('success', 'Image uploaded successfully!');
    }

    public function AllOffers()
    {
        return view('app.adminPanel.products.index');
    }


    public function AllOfferss(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'product_name',
        ];

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $columnIndex = $request->input('order.0.column', 0);
        $order = $columns[$columnIndex] ?? 'id';
        $dir = $request->input('order.0.dir', 'desc');

        $query = Product::query();

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'LIKE', "%{$search}%")
                    ->orWhere('slug', 'LIKE', "%{$search}%");
            });
        }

        $totalData = Product::count();
        $totalFiltered = $query->count();

        $products = $query->orderBy($order, $dir)
            ->offset($start)
            ->limit($limit)
            ->get();

        $data = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'product_name' => $product->product_name,
                'slug' => $product->slug,
                'action' => '<a href="' . route('single.product', $product->slug) . '" class="btn btn-sm btn-primary">View</a>',
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }
}
