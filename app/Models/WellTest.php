<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\EloquentDataTable;

class WellTest extends Model
{
    protected $fillable = [
        'client', 'field', 'well', 'wellhead_tower', 'wellhead_string',
        'choke_size', 'upstream_choke', 'downstream_choke',
        'start_time', 'end_time', 'metadata', 'test_data',
        'calculated_metrics', 'filename','log_file_path','test_type'
    ];

    protected $casts = [
        'metadata' => 'array',
        'test_data' => 'array',
        'calculated_metrics' => 'array',
        'log_file_path' => 'string',
        'test_type' => 'string',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];



     public static function dataTable($query)
    {
        return datatables($query);
    }



    public function getShowUrlAttribute()
    {
        return route('well_tests.show', $this->id);
    }


    public function getCompareUrlAttribute()
    {
        return route('well_tests.compare-results', ['ids' => $this->id]);
    }
}
