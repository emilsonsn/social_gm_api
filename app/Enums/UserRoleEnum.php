<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case Admin = 'Admin';
    case Manager = 'Manager';
    case User = 'User';
}
