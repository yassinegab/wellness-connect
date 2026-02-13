<?php

namespace App\Enum;

enum UserRole: string {
    case PATIENT = 'ROLE_PATIENT';
    case MEDECIN = 'ROLE_MEDECIN';
    case ADMIN = 'ROLE_ADMIN';
}
