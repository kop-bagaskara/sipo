<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'preference_key',
        'preference_value'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get user preference value
     */
    public static function getPreference($userId, $key, $default = null)
    {
        $preference = self::where('user_id', $userId)
            ->where('preference_key', $key)
            ->first();

        return $preference ? $preference->preference_value : $default;
    }

    /**
     * Set user preference value
     */
    public static function setPreference($userId, $key, $value)
    {
        return self::updateOrCreate(
            ['user_id' => $userId, 'preference_key' => $key],
            ['preference_value' => $value]
        );
    }
}
