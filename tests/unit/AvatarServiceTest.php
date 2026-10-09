<?php
namespace Tests\Unit;
use App\Services\Avatar\AvatarService;
use App\Services\Avatar\AvatarStorage;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\Test\CIUnitTestCase;
use RuntimeException;
final class AvatarServiceTest extends CIUnitTestCase
{
    public function testUnsupportedAvatarIsRejected(): void
    {
        $path=tempnam(sys_get_temp_dir(),'avatar'); file_put_contents($path,'not an image');
        $file=new UploadedFile($path,'avatar.txt','text/plain',filesize($path),UPLOAD_ERR_OK);
        $storage=new class implements AvatarStorage { public function available():bool{return true;} public function store(string $f,string $c):void{} public function delete(string $f):void{} public function url(?string $f):string{return '';} public function notice():?string{return null;} };
        $this->expectException(RuntimeException::class); (new AvatarService($storage))->process($file);
    }
    public function testOversizedAvatarIsRejected(): void
    {
        $path=tempnam(sys_get_temp_dir(),'avatar'); file_put_contents($path,str_repeat('x',2*1024*1024+1));
        $file=new UploadedFile($path,'avatar.png','image/png',filesize($path),UPLOAD_ERR_OK);
        $storage=new class implements AvatarStorage { public function available():bool{return true;} public function store(string $f,string $c):void{} public function delete(string $f):void{} public function url(?string $f):string{return '';} public function notice():?string{return null;} };
        $this->expectException(RuntimeException::class); $this->expectExceptionMessage('2 MB'); (new AvatarService($storage))->process($file);
    }
}
