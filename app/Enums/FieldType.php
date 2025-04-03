<?php

namespace App\Enums;

enum FieldType: string
{
    case TEXT = 'text';
    case PASSWORD = 'password';
    case URL = 'url';
    case TEXTAREA = 'textarea';
    case PIN = 'pin';
    case TWO_FA = '2fa';
}
