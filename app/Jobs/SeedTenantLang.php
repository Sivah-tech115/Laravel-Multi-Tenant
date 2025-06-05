<?php

namespace App\Jobs;
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SeedTenantLang implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $translations = include resource_path('lang/tenant/en_translations.php');
    
        $insertData = [];
        foreach ($translations as $key => $value) {
            $exists = DB::table('translations')
                ->where('locale', 'en')
                ->where('key', $key)
                ->exists();
    
            if (!$exists) {
                $insertData[] = [
                    'locale' => 'en',
                    'key' => $key,
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
    
        if (!empty($insertData)) {
            DB::table('translations')->insert($insertData);
        }
    }
    
}
