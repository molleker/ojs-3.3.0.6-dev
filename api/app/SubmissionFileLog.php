<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\SubmissionFileLog
 *
 * @property integer $id
 * @property integer $article_id
 * @property integer $old_file_id
 * @property integer $new_file_id
 * @property integer $user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\SubmissionFileLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SubmissionFileLog whereArticleId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SubmissionFileLog whereOldFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SubmissionFileLog whereNewFileId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SubmissionFileLog whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SubmissionFileLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SubmissionFileLog whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\ArticleFile $oldFile
 * @property-read \App\ArticleFile $newFile
 */
class SubmissionFileLog extends Model
{
    protected $fillable = [
        'old_file_id',
        'new_file_id',
        'user_id',
    ];

    public function oldFile()
    {
        return $this->hasOne(ArticleFile::class, 'file_id', 'old_file_id');
    }

    public function newFile()
    {
        return $this->hasOne(ArticleFile::class, 'file_id', 'new_file_id');
    }
}
