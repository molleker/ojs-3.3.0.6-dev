<?php

namespace App\Http\Controllers;

use App\Article;
use App\ArticleFile;
use App\Components\JournalPresenter;
use App\Journal;
use App\Role;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;


class AuthUserController extends Controller
{
    public function journalsEditor()
    {
        return new JournalPresenter(Auth::user()->journalsByRole(Role::EDITOR_ROLE));
    }
}
