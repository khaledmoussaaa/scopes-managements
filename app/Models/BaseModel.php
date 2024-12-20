<?php

namespace App\Models;

use App\Casts\DateTimeCasting;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
       /**
     * The default attributes that should be cast.
     * These will be merged with the model-specific casts.
     *
     * @var array<string, string>
     */
    protected $defaultCasts = [
        'created_at' => DateTimeCasting::class,
        'updated_at' => DateTimeCasting::class,
    ];

    /**
     * Get the merged casts array from default and model-specific casts.
     *
     * @return array
     */
    public function getCasts()
    {
        return array_merge($this->defaultCasts, $this->casts);
    }
}
