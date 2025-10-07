<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use HosseinHezami\LaravelGemini\Facades\Gemini;
use Illuminate\Support\Facades\Log;
use App\Models\GeminiTable;
class GenerateGeminiTravelPlan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120; 

    protected $code;
    protected $city;
    protected $day;

    public function __construct($code,$city,$day)
    {
        $this->code = $code;
        $this->city = $city;
        $this->day  = $day ;
    }

    public function handle(): void
    { 
        $lang;
        try {
        
       $lang='Arabic';
       if (in_array($this->city, ['Erbil','Sulaymaniyah','Dohuk'])) {
        $lang = 'Kurdish';
        }


   
        
         $raw = Gemini::text()->model('gemini-2.5-flash')
         ->system(
      'You are a travel planner that outputs STRICT JSON only. ' .
        'Use'. $lang .'for ALL values. ' .
        'No Markdown, no code fences, no explanations. ' .
        'IMPORTANT: The "location" value must be the full, proper, and most commonly used name of the famous location in that city (e.g., "Erbil Citadel" or "Sami Abdulrahman Park"), with no descriptive words or prefixes.'
        )
       ->prompt(
        'Create a compact'.$this->day.'-day journey plan for ' . $this->city . '.' .
        'Schema must be exactly: { "days":[{ "day": string, "rows":[{ "time": string, "activity": string, "location": string, "notes": string }] }] }. ' .
        'Use 24-hour time like "09:00", 3 rows per day'
    )
    ->temperature(0.3)
    ->maxTokens(4096)
    ->generate()
    ->content();

        $clean = preg_replace('/^\s*```(?:json)?\s*|\s*```\s*$/m', '', $raw);
        $data = json_decode($clean, true, 512, JSON_THROW_ON_ERROR);

        
        GeminiTable::Create([
            'data'=>$data,
             'code_chat' => $this->code
        ]);        
        } catch (\Throwable $th) {
        Log::error('Gemini Job failed: ' . $th->getMessage());       
        }
    }
}
