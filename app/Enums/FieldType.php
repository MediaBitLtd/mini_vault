<?php

namespace App\Enums;

enum FieldType: string
{
    case TEXT = 'text';
    case PASSWORD = 'password';
    case URL = 'url';
    case TEXTAREA = 'textarea';
}
