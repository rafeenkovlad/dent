<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Telegram_group extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'message'];

    protected $table = 'telegram_group';

    public function scopeGetMessage($query)
    {
        return $query->select('message');
    }
    public $original;
}