<?php
namespace Fhsinchy\Inspire\Controllers;

use Illuminate\Http\Request;
use Fhsinchy\Inspire\Service\InspireService;
use App\Models\User;

class InspirationController
{
    public function getLatestQuot(InspireService $inspire){
        // $user = new User();
        $quote = $inspire->justDoIt();
        return $quote;
    }
}