<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\BulkFeed;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class FeedSourceController extends Controller
{
    public function index()
    {
        $feeds = BulkFeed::all();
        return view('app.adminPanel.feedBulk.index', compact('feeds'));
    }

    public function store(Request $request)
    {
        $request->validate(['zip_url' => 'required|url']);

        BulkFeed::create($request->only('zip_url'));
        return redirect()->route('admin.feeds.index')->with('success', 'Feed URL added.');
    }

    public function viewFeed(BulkFeed $feed)
    {

      
        try {
            // Get local CSV file path
            $relativePath = parse_url($feed->local_file_url, PHP_URL_PATH); // e.g. /bulkfeed/Bulk_awinfeed_1749459415.csv
            $relativePath = ltrim($relativePath, '/'); // remove leading slash if any
        
            // Convert to full filesystem path
            $filePath = public_path($relativePath);
        
            if (!file_exists($filePath)) {
                return back()->withErrors(['error' => 'CSV file not found at ' . $filePath]);
            }
            // dd($feed);
            // Read and parse CSV
            $csv = file_get_contents($filePath);
            $lines = explode("\n", trim($csv));
            $headers = str_getcsv(array_shift($lines));
            $rows = [];
 
            foreach ($lines as $line) {
                if (trim($line) === '') continue;
                $rows[] = str_getcsv($line);
            }

            return view('app.adminPanel.feedBulk.view', compact('feed', 'headers', 'rows'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function import($id)
    {
        $feed = BulkFeed::findOrFail($id);
        $gz = file_get_contents($feed->url);
        $csv = gzdecode($gz);
        $lines = explode("\n", $csv);
        $headers = str_getcsv(array_shift($lines));

        foreach ($lines as $line) {
            $data = array_combine($headers, str_getcsv($line));
            if ($data && !empty($data['Advertiser ID'])) {
                // Your logic to import to DB
                // Example:
                DB::table('imported_feeds')->insert([
                    'advertiser_id' => $data['Advertiser ID'],
                    'advertiser_name' => $data['Advertiser Name'],
                    'url' => $data['URL'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return back()->with('success', 'Feed imported.');
    }
}
