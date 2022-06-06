<?php

use App\Components\OJSLangs;
use Illuminate\Database\Migrations\Migration;

class AddLanguageEditorStatistic extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        OJSLangs::setLang('locale/ru_RU/editor', 'editor.statistic', 'Статистика');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        OJSLangs::removeLang('locale/ru_Ru/editor', 'editor.statistic');
    }
}
