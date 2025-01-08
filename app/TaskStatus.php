<?php

namespace App;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TaskStatus: string implements HasLabel, HasIcon, HasColor
{
    case NotStarted = 'not_started';
    case InProgress = 'in_progress';
    case Review = 'review';
    case Done = 'done';

    public function getLabel(): string
    {
        return match ($this) {
            self::NotStarted => 'Not Started',
            self::InProgress => 'In Progress',
            self::Review => 'Review',
            self::Done => 'Done',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::NotStarted => 'heroicon-o-cursor-arrow-rays',
            self::InProgress => 'heroicon-o-clock',
            self::Review => 'heroicon-o-eye',
            self::Done => 'heroicon-o-check-circle',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::NotStarted => 'gray',
            self::InProgress => 'info',
            self::Review => 'review',
            self::Done =>  'success',
        };
    }
}
