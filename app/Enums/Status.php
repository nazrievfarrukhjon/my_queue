<?php

namespace App\Enums;

enum Status: int
{
    case pending = 1;
    case invited = 2;
    case active = 3;
    case done = 4;
    case skipped = 5;
}
