<?php

namespace App\Domain;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';
    case Unknown = 'unknown';
}
