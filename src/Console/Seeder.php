<?php

namespace NaspersClassifieds\LaravelVersionedSeeder\Console;

/**
 * Seeder.php
 *
 * @author    Pedro Carmo <pedro.carmo@olx.com>
 * @copyright 2016 Naspers Classifieds
 *
 */

use Artisan;
use DB;
use Illuminate\Foundation\Application;

class Seeder
{

    /**
     * @var Application
     */
    private $laravel;


    /**
     * Seeder constructor.
     *
     * @param $laravel
     */
    public function __construct(Application $laravel)
    {
        $this->laravel = $laravel;
    }


    /**
     * @return bool
     */
    public function hasSeedsTable()
    {
        try {
            $res = DB::statement('SELECT * FROM `seeds` LIMIT 1;');

            return $res;
        } catch (\PDOException $e) {
            return false;
        }
    }


    /**
     * @return bool
     */
    public function createSeedsTable()
    {
        $res = DB::statement('CREATE TABLE `seeds` (
                            `seed` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
                            `batch` INT(11) NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

        return $res;
    }


    /**
     * @return int
     */
    public function reset()
    {
        $res = DB::update('UPDATE `seeds` SET `batch` = 0;');

        return $res;
    }


    /**
     *
     */
    public function populateTable()
    {
        $files   = $this->getSeedFiles();
        $records = $this->getCurrentSeeds();

        $recordsArr = array_map(function ($v) {
            return $v->seed;
        }, $records);

        DB::beginTransaction();

        foreach ($files as $file) {
            if (!in_array($file, $recordsArr)) {
                DB::insert("INSERT INTO `seeds` (`seed`, `batch`) VALUES (?, ?);", [$file, 0]);
            }
        }

        DB::commit();
    }


    /**
     * @return \Generator
     */
    public function runUnseeded()
    {
        $seeds       = $this->getUnseededSeeds();
        $latestBatch = $this->getLatestBatch();

        $currentBatch = $latestBatch + 1;

        foreach ($seeds as $seedRow) {
            $seed = $seedRow->seed;
            preg_match("/\d+_(.*)/", $seed, $matches);
            $class = $matches[1];

            Artisan::call('db:seed', ['--class' => $class]);

            DB::update('UPDATE `seeds` SET `batch` = ? WHERE `seed` = ?;', [$currentBatch, $seed]);

            yield $class;
        }
    }


    /**
     * @return array
     */
    public function getAllSeeds()
    {
        $res = DB::select('SELECT * FROM `seeds`;');

        return $res;
    }


    /**
     * @return array
     * @throws \Exception
     */
    protected function getSeedFiles()
    {
        $path = $this->laravel->databasePath() . DIRECTORY_SEPARATOR . 'seeds';

        $files = [];
        foreach (glob($path . "/*.php") as $filename) {
            if (preg_match("/seeds\/(\d+_.*).php$/", $filename, $match)) {
                $files[] = $match[1];
            }
        }

        if (count($files) == 0) {
            throw new \Exception('No seed files present in folder ' . $path);
        }

        return $files;
    }


    /**
     * @return array
     */
    protected function getCurrentSeeds()
    {
        $res = DB::select('SELECT `seed` FROM `seeds`;');

        return $res;
    }


    /**
     * @return array
     */
    protected function getUnseededSeeds()
    {
        $res = DB::select('SELECT `seed` FROM `seeds` WHERE `batch` = 0;');

        return $res;
    }


    /**
     * @return int
     */
    protected function getLatestBatch()
    {
        $res = DB::select('SELECT MAX(`batch`) as max_batch FROM `seeds`;');

        return $res[0]->max_batch;
    }
}