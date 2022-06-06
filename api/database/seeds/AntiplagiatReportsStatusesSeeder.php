<?php

use App\AntiplagiatReportStatus;
use Illuminate\Database\Seeder;

class AntiplagiatReportsStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        collect([
            1 => 'Обрабатывается',
            2 => 'Отчет получен'
        ])->each(function ($title, $id) {
            $status = AntiplagiatReportStatus::firstOrNew(['id' => $id]);
            $status->title = $title;
            $status->save();
        });
    }
}
