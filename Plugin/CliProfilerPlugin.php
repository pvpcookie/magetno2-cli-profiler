<?php

namespace Shaun\Profiler\Plugin;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Helper\Table;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;

class CliProfilerPlugin
{

    const TIME_COLUMN_WIDTH = 10;
    const SQL_COLUMN_WIDTH = 70;
    const PARAM_COLUMN_WIDTH = 30;
    const ROW_SEPARATOR = " ";

    /**
     * @var ConsoleOutput
     */
    private $output;

    public function __construct(ConsoleOutput $output)
    {
        $this->output = $output;
    }

    /**
     * Construct profiler before command runs
     *
     * @param $command
     * @return void
     */
    public function beforeExecute($command)
    {
        $command->_profiler_connection = ObjectManager::getInstance()->get(ResourceConnection::class);
        $command->_profiler_reads = $command->_profiler_connection->getConnection('read')->getProfiler();
    }

    /**
     * Log profiler information to console
     *
     * @param $command
     * @return void
     */
    public function afterExecute($command)
    {

        $output_table = new Table($this->output);

        $output_table_rows = [];
        foreach ($command->_profiler_reads->getQueryProfiles() as $query) {
            $output_table_rows[] = [
                wordwrap(
                    number_format(1000 * $query->getElapsedSecs(), 2).'ms',
                    CliProfilerPlugin::TIME_COLUMN_WIDTH,
                    PHP_EOL
                ),
                "<info>".wordwrap(
                    $query->getQuery(),
                    CliProfilerPlugin::SQL_COLUMN_WIDTH,
                    PHP_EOL
                )."</info>",
                wordwrap(
                    json_encode($query->getQueryParams()),
                    CliProfilerPlugin::PARAM_COLUMN_WIDTH,
                    PHP_EOL
                )
            ];

            if(CliProfilerPlugin::ROW_SEPARATOR){
                $output_table_rows[] = [
                    str_repeat(
                        CliProfilerPlugin::ROW_SEPARATOR,
                        CliProfilerPlugin::TIME_COLUMN_WIDTH
                    ),
                    str_repeat(
                        CliProfilerPlugin::ROW_SEPARATOR,
                        CliProfilerPlugin::SQL_COLUMN_WIDTH
                    ),
                    str_repeat(
                        CliProfilerPlugin::ROW_SEPARATOR,
                        CliProfilerPlugin::PARAM_COLUMN_WIDTH
                    )
                ];
            }

        }

        array_pop($output_table_rows);

        $output_table->setHeaders([
            sprintf(
                "Time %s",
                number_format(1000 * $command->_profiler_reads->getTotalElapsedSecs(), 2).'ms',
            ),
            sprintf(
                "SQL[Total:%s]",
                $command->_profiler_reads->getTotalNumQueries(),
            ),
            'Query params'
        ]);

        $output_table->setRows($output_table_rows);
        $output_table->render();


    }

}
