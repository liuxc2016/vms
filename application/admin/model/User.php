<?php

namespace app\admin\model;

use think\Model;
use think\Db;
class User extends Model
{
    protected $resultSetType = 'collection';    //自定义初始化
    private $role_table = "role";
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        //TODO:自定义的初始化
    }

    public function getUserInfo($userId)
    {
        $list = Db::name("user")->where('id', $userId)->find();
        return $list;
    }

    public function getPerm($perm_id)
    {
        $list = Db::name("perm")->where('id', $perm_id)->find();
        return $list;
    }

    public function delPerm($perm_id)
    {
        $list = Db::name("perm")->where('id', $perm_id)->update(['is_delete'=>1]);
        return $list;
    }

    public function addPerm($permInfo)
    {
        $data = $permInfo;
        $newPermId = Db::name('perm')->insertGetId($data);
        return $newPermId;
    }

    public function savePerm($permInfo)
    {
        $id = $permInfo['id'];
        $data = [
            'name'=>$permInfo['name'],
            'link'=>$permInfo['link'],
            'icon'=>$permInfo['icon'],
        ];
        $ret = Db::name('perm')->where("id", $id)->update($data);
        return $ret;
    }

    public function login($uname, $upass)
    {
        $userInfo = Db::name("user")->where('uname',$uname)
            ->where( 'upass', $upass)
            ->find();
        if(empty($userInfo)){
            return false;
        }elseif ($userInfo['is_delete'] != 0){
            return false;
        }
        return $userInfo;
    }

    public function getUserList()
    {
        $list = Db::name("user")->where(['is_delete'=>0])->select();
        return $list;
    }

    public function getRoleList(){
        $list = Db::name("role")->where(['is_delete'=>0])->select();
        return $list;
    }

    public function getPermList()
    {
        $list = Db::name("perm")->where(['is_delete'=>0])->select();
        return $list;
    }

    public function getRolePermList($roleId)
    {
        $list = Db::name("perm")->where(['is_delete'=>0])->select();

        $rolePerms = Db::name("role_perm")->where(['role_id'=>$roleId])->column('perm_id');
        foreach ($list as $key => $val){
            if(in_array($val['id'], $rolePerms)){
                $list[$key]['checked'] = true;
                $list[$key]['lay_is_checked'] = true;
            }else{
                $list[$key]['checked'] = false;
                $list[$key]['lay_is_checked'] = false;
            }
        }
        return $list;
    }
    public function saveRolePerm($roleId, $permIds)
    {
        $rolePerms = Db::name("role_perm")->where(['role_id'=>$roleId])->column('perm_id');
        $need_sub = array_diff($rolePerms, $permIds);
        $need_add = array_diff($permIds, $rolePerms);
        if(!empty($need_sub)){
            Db::name("role_perm")->where("role_id = $roleId")->where('perm_id','in',$need_sub)->delete();
        }
        foreach ($need_add as $perm_id){
            $data = [
              "role_id"=>$roleId,
              "perm_id"=>$perm_id,
            ];
            Db::name("role_perm")->insert($data);
        }
        return true;
    }

}