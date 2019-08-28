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
}