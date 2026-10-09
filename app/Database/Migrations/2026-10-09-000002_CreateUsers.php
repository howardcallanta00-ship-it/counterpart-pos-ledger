<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
final class CreateUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addField(['id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true], 'username' => ['type' => 'VARCHAR', 'constraint' => 50], 'full_name' => ['type' => 'VARCHAR', 'constraint' => 100], 'role' => ['type' => 'VARCHAR', 'constraint' => 24], 'password' => ['type' => 'VARCHAR', 'constraint' => 255], 'avatar' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true], 'created_at' => ['type' => 'DATETIME'], 'updated_at' => ['type' => 'DATETIME', 'null' => true]]);
        $this->forge->addKey('id', true); $this->forge->addUniqueKey('username'); $this->forge->createTable('users');
    }
    public function down(): void { $this->forge->dropTable('users'); }
}
