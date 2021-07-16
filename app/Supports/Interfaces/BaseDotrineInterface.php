<?php

namespace App\Supports\Interfaces;

interface BaseDotrineInterface
{
    public function flush();

    public function beginTransaction();

    public function rollback();

    public function commit();
}
