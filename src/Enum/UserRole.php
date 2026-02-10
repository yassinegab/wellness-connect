<?php

namespace App\Enum;

// src/Enum/UserRole.php
enum UserRole: string
{
    case PATIENT = 'patient';
    case MEDECIN = 'medecin';
    case ADMIN = 'admin';
}
