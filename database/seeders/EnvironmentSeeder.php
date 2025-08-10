<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Environment;
class EnvironmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
    public function run(): void
    {$types=['cabin','lake'];
    
        for ($i=0; $i <20; $i++) { 
           $env = new Environment([
            'phone' => '1234567890',
            'latitude' => '35.6895',
            'longitude' => '139.6917',
            'city_id' => 1,
            'type' => $types[$i%2],
        ]);

        // English translation
        $env->translateOrNew('en')->name = 'Blue Lake';
        $env->translateOrNew('en')->description = 'A beautiful blue lake.';

        // Arabic translation
        $env->translateOrNew('ar')->name = 'البحيرة الزرقاء';
        $env->translateOrNew('ar')->description = 'بحيرة زرقاء جميلة.';

        $env->save();
        }
        

        // You can add more environments similarly

       
    
    }
    
}
