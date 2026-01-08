<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestData extends Model
{
    protected $fillable = [
        'well_test_id',
        'data_row', // JSON field to store the actual data row
        'row_index', // Position of the row in the original file
    ];

    protected $casts = [
        'data_row' => 'array', // Store the dynamic data as JSON
        'row_index' => 'integer',
    ];

    /**
     * Get the well test that owns this test data
     */
    public function wellTest(): BelongsTo
    {
        return $this->belongsTo(WellTest::class, 'well_test_id');
    }

    /**
     * Get the dynamic data columns from the data_row
     */
    public function getColumnsAttribute()
    {
        return array_keys($this->data_row);
    }

    /**
     * Get a specific value by column name
     */
    public function getValue(string $column)
    {
        return $this->data_row[$column] ?? null;
    }

    /**
     * Get all numeric values from the row
     */
    public function getNumericValues()
    {
        $numericValues = [];
        foreach ($this->data_row as $key => $value) {
            if (is_numeric($value)) {
                $numericValues[$key] = (float) $value;
            }
        }
        return $numericValues;
    }
}
