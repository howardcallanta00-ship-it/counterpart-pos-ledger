<?php
namespace App\Models;
use CodeIgniter\Model;
final class UserModel extends Model
{
    public const ROLES = ['Administrator', 'Manager', 'Cashier'];
    protected $table = 'users'; protected $primaryKey = 'id'; protected $returnType = 'array';
    protected $allowedFields = ['username', 'full_name', 'role', 'password', 'avatar']; protected $useTimestamps = true;
}
