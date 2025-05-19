<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ImportAwinFeedJob;
use App\Models\Import;

class ProductImportController extends Controller
{
    public function showForm()
    {
        $imports = Import::orderBy('created_at', 'desc')->get();

        // dd($imports);
        return view('app.feed.import', compact('imports'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'zip_url' => 'required|url',
        ]);

        $zipUrl = $request->input('zip_url');

        $import = Import::create([
            'zip_url' => $zipUrl,
            'status' => 'pending',
        ]);


        // Dispatch the import job to the queue
        ImportAwinFeedJob::dispatch($zipUrl, $import->id);

        return back()->with('success', 'Import started. Import ID: ' . $import->id);
    }
}
