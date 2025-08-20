<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Pending = 'pending';
    case In_progress = 'in-progress';
    case Completed = 'completed';
}
