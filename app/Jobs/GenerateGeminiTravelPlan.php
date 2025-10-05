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

    public $timeout = 120; // Give it time to finish

    protected $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function handle(): void
    {
        try {
            //code...
        
        $raw = Gemini::text()->model('gemini-2.5-flash')
            ->system(
                'You are a travel planner that outputs STRICT JSON only. ' .
                'Use Kurdish (Sorani, Arabic script) for ALL values. ' .
                'Do not use Latin letters. No Markdown, no code fences, no explanations.'
            )
            ->prompt(
                'Create a compact 1-day journey plan for Erbil (هەولێر). ' .
                'Schema must be exactly: { "days":[{ "day": string, "rows":[{ "time": string, "activity": string, "location": string, "notes": string }] }] }. ' .
                'Use 24-hour time like "09:00", 5–6 rows per day. Values ONLY in Sorani (Arabic script).'
            )
            ->temperature(0.3)
            ->maxTokens(2048)
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
