<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'status',
    ];

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
