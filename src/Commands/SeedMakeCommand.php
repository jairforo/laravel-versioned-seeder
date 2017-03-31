<?php
/**
 * SeedMakeCommand.php
 *
 * @author    Pedro Carmo <pedro.carmo@olx.com>
 * @copyright 2016 Naspers Classifieds
 *
 */

namespace NaspersClassifieds\LaravelVersionedSeeder\Commands;

use Illuminate\Database\Console\Seeds\SeederMakeCommand;

class SeedMakeCommand extends SeederMakeCommand
{



    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'seed:make';

    /**
     * Get the destination class path.
     *
     * @param  string $name
     *
     * @return string
     */
    protected function getPath($name)
    {
        return $this->laravel->databasePath() . '/seeds/' . date('Ymd') . "_" . $name . '.php';
    }
}
