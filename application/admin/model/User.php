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

    public function getUserList()
    {
        return self::all(['is_delete'=>0])->toArray();
    }

    public function getRoleList(){
        $list = Db::name("role")->where(['is_delete'=>0])->select();
        return $list;
    }
}