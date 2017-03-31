<?php

namespace NaspersClassifieds\LaravelVersionedSeeder\Commands;

/**
 * Seed.php
 *
 * @author    Pedro Carmo <pedro.carmo@olx.com>
 * @copyright 2016 Naspers Classifieds
 *
 */

use Config;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class SeedCommand extends Command
{

    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with seeds that have not been ran yet';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $seeder = new Seeder($this->laravel);

        if (!$this->confirmToProceed()) {
            return;
        }

        try {
            // disable cache for seeding purposes
            Config::set('app.cache', false);

            if (!$seeder->hasSeedsTable()) {
                $res = $seeder->createSeedsTable();

                if ($res) {
                    $this->output->writeln('<info>Seed table created</info>');
                } else {
                    $this->output->writeln('<error>Seed table could not be created!</error>');

                    return;
                }
            }

            $seeder->populateTable();

            foreach ($seeder->runUnseeded() as $class) {
                $this->output->writeln("<info>$class</info>: seeded");
            }

        } catch (Exception $e) {
            $this->output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }
}
