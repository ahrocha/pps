<?php

namespace App\Core;

interface ContainerInterface
{
    public function get(string $id);
}
