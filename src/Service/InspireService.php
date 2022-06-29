<?php

namespace Fhsinchy\Inspire\Service;

use Illuminate\Support\Facades\Http;
use Fhsinchy\Inspire\Traits\sampleTraits;

class InspireService {
    
    use sampleTraits;

    public function justDoIt() {
        dd($this -> traitTest());
        $response = Http::get('https://inspiration.goprogram.ai/');

        return $response['quote'] . ' -' . $response['author'];
    }
}