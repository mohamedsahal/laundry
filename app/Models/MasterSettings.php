<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class MasterSettings extends Model
{
    use HasFactory;
    protected $table = 'master_settings';
    protected $fillable = ['master_title', 'master_value'];
    public $timestamps = false;
    
        /* master settings value update settings */
        public function siteData(){
            $siteInfo = [];
            
            if (!Schema::hasTable($this->table)) {
                return $siteInfo;
            }
            
            foreach($this->get() as $value) {
                $siteInfo[$value['master_title']] = $value['master_value'];
            }
            
            return $siteInfo;
        }
}
