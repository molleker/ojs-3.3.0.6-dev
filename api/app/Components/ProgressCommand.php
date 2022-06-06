<?php


namespace App\Components;


use Illuminate\Console\Command;
use Illuminate\Log\Writer;
use Symfony\Component\Console\Helper\ProgressBar;

trait ProgressCommand
{
    /** @var  ProgressBar */
    private $bar;

    /** @var Writer */
    protected $logger;

    /**
     * Создаёт прогрессбар и файл для лога (если $log_name !== false)
     *
     * @param $count            int     кол-во пунктов для прогрессбара
     * @param null $log_name    string  название лог-файла. Если false - лог-файл не будет создан
     *
     * @throws \Exception
     */
    protected function init($count, $log_name = null)
    {
        if (! ($this instanceof Command)) {
            throw new \Exception('This is not Illuminate\Console\Command class');
        }

        $this->bar = $this->output->createProgressBar($count);
        $this->bar->setFormat(' [%bar%] %current%/%max% (%percent:3s%%) %elapsed:6s%/%estimated:-6s% %memory:6s%');
    }
}
