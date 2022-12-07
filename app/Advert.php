<?php

namespace App;

use App\CPU\Helpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Advert extends Model
{
    use HasFactory;
    protected $table = 'adverts';
    protected $guarded = [];

    public function translations()
    {
        return $this->morphMany('App\Model\Translation', 'translationable');
    }
    public function scopeActive(){
        return $this->where('status',1);
    }


    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                if (strpos(url()->current(), '/api')){
                    return $query->where('locale', App::getLocale());
                }else{
                    return $query->where('locale', Helpers::default_lang());
                }
            }]);
        });
    }
}
