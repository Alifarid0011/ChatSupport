<?php


namespace App\traits;
use Hekmatinasser\Verta\Verta;

trait Shamsi
{
    public function getShamsiCreatedAtAttribute($data)
    {
        return verta($this->created_at);
    }
    public function getShamsiUpdatedAtAttribute($data)
    {
        return verta($this->updated_at);
    }
//    public function getCreatedAtAttribute($data)
//    {
//        return verta($data);
//    }
//    public function getUpdatedAtAttribute($data)
//    {
//        return verta($data);
//    }
    public function getMakeAtAttribute($data)
    {
        return verta($this->shamsi_created_at)->formatDifference();
    }

}