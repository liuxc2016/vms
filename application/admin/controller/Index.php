<?php
namespace app\admin\controller;

use think\Session;
use app\admin\model\User;
use think\Config;
class Index extends AdminBaseController
{

    public function main()
    {
        echo 'admin_index';
        exit(0);
    }

    /**
     * @return array
     */
    public function index()
    {
        $this->initUser();
        $top_menus = [];
        foreach ($this->permList as $key=> $val){
            if($val['pid'] == 0){
                $top_menus[] = $val;
            }
        }
        return $this->fetch('index/index', ['top_menus'=>$top_menus]);
    }

    public function login()
    {
        if(request()->isPost()){
            $params = request()->param();
            $uname = empty($params['uname'])?'':$params['uname'];
            $upass = empty($params['upass'])?'':$params['upass'];
            $ucode = empty($params['ucode'])?'':$params['ucode'];
            if(empty($uname) or empty($upass)){
                return json(['status'=>0, 'info'=>'登陆失败1！']);
            }

            $user = new User();
            $userInfo = $user->login($uname, $upass);
            if(!$userInfo){
                return json(['status'=>0, 'info'=>'登陆失败2！'.$uname.$upass]);
            }else{
                $user->saveLoginRecord($userInfo, $this->request->ip());

                Session::set("userInfo", $userInfo);
                $this->userInfo = $userInfo;
                $this->permList = $user->getRolePermList($this->userInfo['role_id']);
                Session::set("permList", $this->permList );
                return json(['status'=>1, 'url'=>'/admin/index/index', 'info'=>'login ok!'],200);
            }


        }else{
            return $this->fetch('index/login');
        }
    }

    public function logout(){
        Session::set("userInfo", null);
        Session::set("userInfo", null);
        $this->redirect("/admin/index/login");

    }

    //文件上传
    public function upload()
    {

        $file = request()->file("file");
        if(empty($file)){
            return json([
                'code'=>'0',
                'msg'=>"请选择上传文件",
                'data'=>['src'=>''],

            ]);
        }
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            $file_path = Config::get("file_url")."/public/uploads/".date("Ymd")."/".$info->getFilename();
            return json([
                'code'=>'0',
                'msg'=>'上传成功',
                'data'=>['src'=>$file_path],

            ]);
        }else{
            $err = $file->getError();
            return json([
                'code'=>'0',
                'msg'=>$err,
                'data'=>['src'=>''],

            ]);
        }

    }

}