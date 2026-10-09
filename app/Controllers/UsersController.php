<?php
namespace App\Controllers;
use App\Models\UserModel;
use App\Services\Avatar\AvatarService;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;
final class UsersController extends BaseController
{
    public function index(): string
    {
        $avatars = AvatarService::make();
        return view('users/index', ['title'=>'Staff accounts','users'=>(new UserModel())->orderBy('full_name')->findAll(),'avatarStorage'=>$avatars->storage()]);
    }
    public function new(): string { $a=AvatarService::make(); return view('users/form',['title'=>'New staff account','user'=>null,'roles'=>UserModel::ROLES,'avatarNotice'=>$a->storage()->notice(),'avatarUrl'=>$a->storage()->url(null)]); }
    public function create() { return $this->save(); }
    public function edit(int $id): string
    {
        $user=(new UserModel())->find($id); if(!$user){throw PageNotFoundException::forPageNotFound('Staff account not found.');}
        $a=AvatarService::make(); return view('users/form',['title'=>'Edit staff account','user'=>$user,'roles'=>UserModel::ROLES,'avatarNotice'=>$a->storage()->notice(),'avatarUrl'=>$a->storage()->url($user['avatar'])]);
    }
    public function update(int $id) { return $this->save($id); }
    private function save(?int $id=null)
    {
        $model=new UserModel(); $existing=$id ? $model->find($id) : null; if($id && !$existing){throw PageNotFoundException::forPageNotFound('Staff account not found.');}
        $rules=['username'=>'required|max_length[50]|regex_match[/^[A-Za-z0-9._-]+$/]','full_name'=>'required|min_length[2]|max_length[100]','role'=>'required|in_list['.implode(',',UserModel::ROLES).']'];
        $password=(string)$this->request->getPost('password');
        if(!$id || $password!==''){ $rules['password']='required|min_length[10]|max_length[128]'; $rules['password_confirm']='required|matches[password]'; }
        if(!$this->validate($rules)){return redirect()->back()->withInput()->with('errors',$this->validator->getErrors());}
        $username=mb_strtolower(trim((string)$this->request->getPost('username')));
        $duplicate=$model->where('LOWER(username)',$username); if($id){$duplicate->where('id !=',$id);} if($duplicate->first()){return redirect()->back()->withInput()->with('errors',['username'=>'That username is already in use.']);}
        $avatarService=AvatarService::make(); $newAvatar=null;
        try{$newAvatar=$avatarService->process($this->request->getFile('avatar'));}catch(RuntimeException $e){return redirect()->back()->withInput()->with('errors',['avatar'=>$e->getMessage()]);}
        $data=['username'=>$username,'full_name'=>preg_replace('/\s+/u',' ',trim((string)$this->request->getPost('full_name'))),'role'=>(string)$this->request->getPost('role')];
        if($password!==''){$data['password']=password_hash($password,PASSWORD_DEFAULT);} if($newAvatar){$data['avatar']=$newAvatar;}
        $db=db_connect(); $db->transStart(); $ok=$id ? $model->update($id,$data) : $model->insert($data); $db->transComplete();
        if(!$ok || !$db->transStatus()){if($newAvatar){$avatarService->storage()->delete($newAvatar);} return redirect()->back()->withInput()->with('errors',$model->errors() ?: ['form'=>'The account could not be saved.']);}
        if($newAvatar && !empty($existing['avatar'])){$avatarService->storage()->delete($existing['avatar']);}
        return redirect()->to('/users')->with('success',$id ? 'Staff account updated.' : 'Staff account created.');
    }
}
