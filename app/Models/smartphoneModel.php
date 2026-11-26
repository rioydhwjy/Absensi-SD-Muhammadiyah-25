<?php

namespace App\Models;

use CodeIgniter\Model;

class SmartphoneModel extends Model
{
    protected $table      = 'data_hp'; 
    protected $primaryKey = 'model';   
    protected $allowedFields = [
        'brand_name', 'model', 'price_idr', 'spec_score', 'ram', 
        'memory', 'battery_capacity_mah', 'fast_charging_w', 'rear_camera'
    ];
}