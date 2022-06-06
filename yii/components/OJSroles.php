<?php
/**
 * Created by PhpStorm.
 * User: antshater
 * Date: 05.04.15
 * Time: 23:03
 */

namespace app\components;

use Yii;

class OJSroles extends \yii\base\Component
{
    private $rolesId = [
        'user.role.siteAdmin' => 0x00000001,
        'user.role.manager' => 0x00000010,
        'user.role.editor' => 0x00000100,
        'user.role.sectionEditor' => 0x00000200,
        'user.role.layoutEditor' => 0x00000300,
        'user.role.reviewer' => 0x00001000,
        'user.role.copyeditor' => 0x00002000,
        'user.role.proofreader' => 0x00003000,
        'user.role.author' => 0x00010000,
        'user.role.reader' => 0x00100000,
        'user.role.subscriptionManager' => 0x00200000,
    ];
    private $roleDescription = array();

    public function init()
    {
        parent::init();
        $vocabulary = new \SimpleXMLElement(file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/locale/ru_RU/locale.xml'));

        foreach ($vocabulary->message as $mess) {
            $this->roleDescription[(string) $mess->attributes()->key] = (string) $mess;
        }
        $this->rolesId = array_flip($this->rolesId);
    }

    public function getRoleName($id)
    {
        return isset($this->rolesId[$id]) && isset($this->roleDescription[$this->rolesId[$id]]) ? $this->roleDescription[$this->rolesId[$id]] : '';
    }

}