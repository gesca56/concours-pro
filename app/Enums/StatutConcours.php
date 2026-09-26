<?php

namespace App\Enums;

enum StatutConcours: string
{
    case Brouillon = 'brouillon';
    case Ouvert = 'ouvert';
    case Cloture = 'cloture';
    case Deliberation = 'deliberation';
    case Termine = 'termine';
}
