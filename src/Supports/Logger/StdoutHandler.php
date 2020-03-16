<?php
declare (strict_types=1);

namespace Smalls\Pay\Supports\Logger;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class StdoutHandler extends AbstractProcessingHandler
{

    private $output;

    /**
     * @param int $level
     * @param bool $bubble
     * @param OutputInterface|null $output
     */
    public function __construct($level = Logger::DEBUG, $bubble = true, ?OutputInterface $output = null)
    {
        $this->output = $output ?? new ConsoleOutput();
        parent::__construct($level, $bubble);
    }

    /**
     * @param array $record
     */
    protected function write(array $record): void
    {
        $this->output->writeln($record['formatted']);
    }
}
