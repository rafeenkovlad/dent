<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Wp_user extends Model
{
    use HasFactory;

    protected $fillable = ['id','user_login', 'user_pass', 'user_registered'];

    protected $primaryKey = 'ID';

    /**
     * Формат хранения столбцов с датами модели.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    const UPDATED_AT = 'user_registered';
}