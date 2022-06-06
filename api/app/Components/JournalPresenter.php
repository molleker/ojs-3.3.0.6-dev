<?php


namespace App\Components;


use App\Journal;
use Illuminate\Database\Eloquent\Model;

class JournalPresenter extends Presenter
{

    /**
     * @param Journal $journal
     * @return array
     */
    protected function transform(Model $journal)
    {
       return [
           'journal_id' => $journal->journal_id,
           'title' => $journal->title,
           'path' => $journal->path,
       ];
    }
}