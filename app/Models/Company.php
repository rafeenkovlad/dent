<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    protected $fillable = ['company_name', 'contact', 'info'];
    /**
     * Таблица, связанная с моделью.
     *
     * @var string
     */
    protected $table = 'company';
}