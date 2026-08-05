<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'name',
        'email',
        'google_sub',
        'auth_provider',
        'avatar_url',
        'email_verified_at',
        'password_hash',
        'status',
        'last_login_at',
        'first_login_at',
    ];
}
