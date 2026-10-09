<?php
namespace Tests\Feature;
use App\Database\Seeds\DemoSeeder;
use App\Models\CustomerModel;
use App\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
final class ApplicationFeatureTest extends CIUnitTestCase
{
    use DatabaseTestTrait, FeatureTestTrait;
    protected $refresh = true;
    protected $seed = DemoSeeder::class;
    protected $namespace = 'App';
    public function testPublicPagesAndHealth(): void { $this->get('/')->assertOK(); $this->get('/about')->assertOK(); $health=$this->get('/health'); $health->assertOK(); $health->assertJSONFragment(['status'=>'ok']); }
    public function testProtectedPagesRedirectToLogin(): void { $this->get('/customers')->assertRedirectTo('/login'); $this->get('/users')->assertRedirectTo('/login'); }
    public function testValidAndInvalidLogin(): void
    {
        $valid=$this->postCsrf('/login',['username'=>'admin','password'=>'Counterpart!2026']); $valid->assertRedirectTo('/customers'); $this->assertNotNull(session()->get('auth_user_id'));
        session()->destroy(); $invalid=$this->postCsrf('/login',['username'=>'admin','password'=>'wrong-password']); $invalid->assertRedirect(); $this->assertNull(session()->get('auth_user_id'));
    }
    public function testLogoutClearsAuthentication(): void { $this->postCsrf('/logout',[],['auth_user_id'=>1])->assertRedirectTo('/login'); $this->assertNull(session()->get('auth_user_id')); }
    public function testCustomerValidationCreationAndEditing(): void
    {
        $session=['auth_user_id'=>1]; $this->postCsrf('/customers',['full_name'=>'','email'=>'bad'],$session)->assertRedirect();
        $this->assertSame(5,(new CustomerModel())->countAllResults());
        $this->postCsrf('/customers',['full_name'=>'  Lila   Reyes  ','email'=>'LILA@example.test','phone'=>'+63 912 000 7788'],$session)->assertRedirectTo('/customers');
        $created=(new CustomerModel())->where('email','lila@example.test')->first(); $this->assertSame('Lila Reyes',$created['full_name']);
        $this->postCsrf('/customers/'.$created['id'],['full_name'=>'Lila Reyes','email'=>'lila@example.test','phone'=>''],$session)->assertRedirectTo('/customers');
        $this->assertNull((new CustomerModel())->find($created['id'])['phone']);
    }
    public function testUsernameRulesAndPasswordReplacement(): void
    {
        $session=['auth_user_id'=>1]; $count=(new UserModel())->countAllResults();
        $this->postCsrf('/users',['username'=>'ADMIN','full_name'=>'Other Admin','role'=>'Manager','password'=>'StrongPass!4','password_confirm'=>'StrongPass!4'],$session)->assertRedirect();
        $this->assertSame($count,(new UserModel())->countAllResults());
        $admin=(new UserModel())->where('username','admin')->first();
        $this->postCsrf('/users/'.$admin['id'],['username'=>'admin','full_name'=>$admin['full_name'],'role'=>$admin['role'],'password'=>'Replacement!54','password_confirm'=>'Replacement!54'],$session)->assertRedirectTo('/users');
        $this->assertTrue(password_verify('Replacement!54',(new UserModel())->find($admin['id'])['password']));
        $other=(new UserModel())->where('username','soren.lim')->first();
        $this->postCsrf('/users/'.$admin['id'],['username'=>$other['username'],'full_name'=>$admin['full_name'],'role'=>$admin['role'],'password'=>'','password_confirm'=>''],$session)->assertRedirect();
        $this->assertSame('admin',(new UserModel())->find($admin['id'])['username']);
    }
    public function testMissingRecordsReturn404(): void { $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class); $this->withSession(['auth_user_id'=>1])->get('/customers/99999/edit'); }
    public function testCsrfRejectsMissingTokenWhenFiltersEnabled(): void { $this->expectException(\CodeIgniter\Security\Exceptions\SecurityException::class); $this->withSession(['auth_user_id'=>1])->post('/customers',['full_name'=>'Valid Name','email'=>'valid@example.test']); }
    private function postCsrf(string $path,array $data,array $session=[])
    {
        $security=service('security'); $hash=(string)$security->getHash();
        return $this->withSession($session)->withHeaders(['Cookie'=>$security->getCookieName().'='.$hash])->post($path,array_merge($data,[$security->getTokenName()=>$hash]));
    }
}
