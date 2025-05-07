<?php

namespace App\Http\Controllers;

use App\Http\Traits\ArchiveTrait;
use App\Http\Traits\ResponsesTrait;

abstract class Controller
{
    use ArchiveTrait,ResponsesTrait;
}
