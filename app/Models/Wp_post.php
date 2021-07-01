<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Wp_post extends Model
{
    use HasFactory;

    protected $fillable = ['post_author','post_date', 'post_content', 'post_title', 'post_type', 'comment_count'];

    protected $primaryKey = 'ID';

    /**
     * Формат хранения столбцов с датами модели.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d H:i:s';

    const UPDATED_AT = 'post_modifield';
}