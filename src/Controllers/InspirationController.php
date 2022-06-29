<?php
namespace Fhsinchy\Inspire\Controllers;

use Illuminate\Http\Request;
use Fhsinchy\Inspire\Service\InspireService;

class InspirationController
{
    public function getLatestQuot(InspireService $inspire){
        $quote = $inspire->justDoIt();
        return $quote;
    }
}