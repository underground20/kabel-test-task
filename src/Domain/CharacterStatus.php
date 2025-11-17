<?php

namespace App\Domain;

enum CharacterStatus: string
{
    case Alive = 'alive';
    case Dead = 'dead';
    case Unknown = 'unknown';
}
