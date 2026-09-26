<?php

namespace App\Enums;

enum Role: string
{
    case Candidat = 'candidat';
    case Receptionniste = 'receptionniste';
    case Medecin = 'medecin';
    case Enseignant = 'enseignant';
    case Administration = 'administration';
}
