<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ArticleStatus
 *
 * @mixin \Eloquent
 */
class ArticleStatus extends Model
{
    const REJECTED = 0;
    const READY = 10;
}
