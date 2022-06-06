<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ArticleStatusSubmissionFile
 *
 * @mixin \Eloquent
 */
class ArticleStatusSubmissionFile extends Model
{
    const INITIAL = 0;
    const AUTHOR_REPLACED_BEFORE_ANTIPLAGIAT = 1;
    const AUTHOR_REPLACED_AFTER_ANTIPLAGIAT = 2;
}
