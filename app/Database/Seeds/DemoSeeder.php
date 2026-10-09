<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;
use RuntimeException;
final class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (ENVIRONMENT !== 'testing' && ! filter_var(env('DEMO_SEED_ENABLED', false), FILTER_VALIDATE_BOOL)) { throw new RuntimeException('Set DEMO_SEED_ENABLED=true to load demo records outside testing.'); }
        $now = date('Y-m-d H:i:s');
        $this->db->table('customers')->insertBatch([
            ['full_name'=>'Amihan Mercado','email'=>'amihan.mercado@example.test','phone'=>'+63 917 482 1160','created_at'=>$now], ['full_name'=>'Mateo Villareal-Santos','email'=>'mateo.villareal.santos@example.test','phone'=>'(02) 8555-0194','created_at'=>$now], ['full_name'=>'Nur Aisyah Rahman','email'=>'aisyah.rahman@example.test','phone'=>null,'created_at'=>$now], ['full_name'=>'Leandro Costa','email'=>'leandro.costa@example.test','phone'=>'+351 912 340 771','created_at'=>$now], ['full_name'=>'Yuki Nakamori','email'=>'yuki.nakamori.longaddress@example.test','phone'=>'090-4832-1176','created_at'=>$now]
        ]);
        $this->db->table('users')->insertBatch([
            ['username'=>(string)env('DEMO_ADMIN_USERNAME','admin'),'full_name'=>'Mara Dela Cruz','role'=>'Administrator','password'=>password_hash((string)env('DEMO_ADMIN_PASSWORD','Counterpart!2026'),PASSWORD_DEFAULT),'created_at'=>$now], ['username'=>'soren.lim','full_name'=>'Soren Lim','role'=>'Manager','password'=>password_hash('Ledger!4820',PASSWORD_DEFAULT),'created_at'=>$now], ['username'=>'ines.ramos','full_name'=>'Ines Ramos-Cortez','role'=>'Cashier','password'=>password_hash('Counter!7216',PASSWORD_DEFAULT),'created_at'=>$now], ['username'=>'adil.khan','full_name'=>'Adil Khan','role'=>'Cashier','password'=>password_hash('Counter!3095',PASSWORD_DEFAULT),'created_at'=>$now], ['username'=>'celine.fong','full_name'=>'Celine Fong-Watanabe','role'=>'Manager','password'=>password_hash('Ledger!8841',PASSWORD_DEFAULT),'created_at'=>$now]
        ]);
    }
}
