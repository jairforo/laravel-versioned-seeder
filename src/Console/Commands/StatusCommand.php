<?php
/**
 * StatusCommand.php
 *
 * @author    Pedro Carmo <pedro.carmo@olx.com>
 * @copyright 2016 Naspers Classifieds
 *
 */

namespace NaspersClassifieds\LaravelVersionedSeeder\Console\Commands;

use Illuminate\Database\Console\Migrations\BaseCommand;

class StatusCommand extends BaseCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seed:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the status of each seed';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $seeder = new Seeder($this->laravel);

        try {
            if (!$seeder->hasSeedsTable()) {
                $this->output->writeln('<comment>Seed table not found.</comment>');

                return;
            }

            $seeder->populateTable();

            $seeds    = [];
            $allSeeds = $seeder->getAllSeeds();
            foreach ($allSeeds as $row) {
                preg_match("/\d+_(.*)/", $row->seed, $matches);
                $class = $matches[1];

                $seeds[] = $row->batch != 0 ? ['<info>Y</info>', $class] : ['<fg=red>N</fg=red>', $class];
            }

            if (count($seeds) > 0) {
                $this->table(['Ran?', 'Seed'], $seeds);
            } else {
                $this->error('No seeds found');
            }

        } catch (Exception $e) {
            $this->output->writeln('<error>' . $e->getMessage() . '</error>');
        }

    }
}
