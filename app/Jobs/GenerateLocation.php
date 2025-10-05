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

class GenerateLocation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $timeout = 120; // Give it time to finish

    protected $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function handle(): void
    {
        try {
        $data = GeminiTable::where('code_chat', $this->code)->first();
       $response = Gemini::text()
    ->model('gemini-2.5-flash')
    ->system('You are a helpful assistant.')
    ->prompt('give me a data of parki sami abdulrahman lat and long')
    ->temperature( 0.7)
    ->maxTokens(1024)
    ->generate();
     
        GeminiTable::Create([
             'data'=>$response->content(),
             'code_chat' => $this->code
        ]); 
         
        } catch (\Throwable $th) {
        Log::error('Gemini Job failed: ' . $th->getMessage());       
        }
    }
}
