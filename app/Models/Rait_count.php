<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rait_count extends Model
{
    use HasFactory;

    protected $fillable = ['name_co_and_wo', 'wp_users_id', 'like_array', 'wp_posts_id'];

    protected $table = 'rait';

    protected function scopeRait($query, $wp_user_id)
    {
        return $query->where('wp_users_id', $wp_user_id );
    }

    public function scopeTotalLikeUser($query, $wp_user_id)
    {
        return $query->Rait($wp_user_id)->select('like_array');
    }

    public $original;
}