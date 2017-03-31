<?php

namespace NaspersClassifieds\LaravelVersionedSeeder\Commands;

/**
 * ResetCommand.php
 *
 * @author    Pedro Carmo <pedro.carmo@olx.com>
 * @copyright 2016 Naspers Classifieds
 *
 */

use Exception;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class ResetCommand extends Command
{

    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seed:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark all seeds as not ran to reset the seeding process. USE WITH CAUTION!';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $seeder = new Seeder($this->laravel);

        if (!$this->confirmToProceed()) {
            return;
        }

        if (!$seeder->hasSeedsTable()) {
            $this->output->writeln('<comment>Seed table not found.</comment>');

            return;
        }

        try {
            $count = $seeder->reset();

            if ($count > 0) {
                $this->output->writeln('<info>Seeds sucessfully reset!</info>');
            } else {
                $this->output->writeln('<comment>No seeds to revert!</comment>');
            }

            $seeder->populateTable();
        } catch (Exception $e) {
            $this->output->writeln('<error>' . $e->getMessage() . '</error>');
        }

    }
}
