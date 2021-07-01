<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Like_count_profile extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'wp_users_id', 'like_col'];


    protected $table = 'like_sum';
}