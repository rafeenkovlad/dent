<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Gods extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'id', 'name', 'sirial_number', 'made_in_company', 'price', 'litle_info', 'img_url'];


    protected $table = 'excel_list';
}