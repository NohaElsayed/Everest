<?php
namespace App\CentralLogics;
use App\Models\Zone;
use App\Models\AddOn;
use App\Models\Order;
use App\Models\Review;
use App\Models\TimeLog;
use App\Models\Currency;
use App\Models\DMReview;
use App\Mail\OrderPlaced;
use Illuminate\Support\Carbon;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\CentralLogics\RestaurantLogic;
use Illuminate\Support\Facades\Storage;
use Laravelpkg\Laravelchk\Http\Controllers\LaravelchkController;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class Helpers
{

  
    public static function format_coordiantes($coordinates)
    {
        $data = [];
        foreach ($coordinates as $coord) {
            $data[] = (object)['lat' => $coord->getlat(), 'lng' => $coord->getlng()];
        }
        return $data;
    }

  
}
