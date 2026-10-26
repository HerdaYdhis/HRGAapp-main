<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    /**
     * user
     *
     * @return void
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /**
     * getImageAttribute
     *
     * @param  mixed $image
     * @return void
     */
    public function getImageAttribute($image)
    {
        if ($image != null) :
            return asset('storage/' . $image);
        else :
            return 'https://ui-avatars.com/api/?name=' . 'x' . '&background=4e73df&color=ffffff&size=30';
        endif;

        //accesor

    }
    public function details()
    {
        return $this->hasMany(TaskDetail::class, 'task_id');
    }
}
