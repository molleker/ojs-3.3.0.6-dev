<?php

namespace App\Http\Requests;


use App\States\ArticleFileState;
use Illuminate\Foundation\Http\FormRequest;

class StoreArticleFileRequest extends FormRequest
{

    public function authorize()
    {
        if ($this->input('type') === ArticleFileState::SUBMISSION) {
            return $this->route('article')->edit_submission_file;
        }

        return true;
    }

    public function rules()
    {
        return [];
    }

}