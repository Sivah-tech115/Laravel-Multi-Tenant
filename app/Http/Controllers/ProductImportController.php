<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Import;

class ProductImportController extends Controller
{
    public function showForm()
    {
        $imports = Import::orderBy('created_at', 'desc')->get();

        return view('app.adminPanel.feed.import', compact('imports'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'zip_url' => 'required|url',
        ]);

        $zipUrl = $request->input('zip_url');

        Import::create([
            'zip_url' => $zipUrl,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Import started');
    }

    public function import_reset($id)
    {
        $import = Import::findOrFail($id);
        $import->status = 'pending';
        $import->log = 'import restarted';
        $import->reimport_status = '1';
        $import->save();
    
        return redirect()->back()->with('success', 'Feed import restart successfully.');
    }
    


}
