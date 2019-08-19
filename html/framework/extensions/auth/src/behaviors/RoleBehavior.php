<?php
/**
 * Created by PhpStorm.
 * User: krok
 * Date: 19.04.18
 * Time: 17:13
 */

namespace krok\auth\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\rbac\ManagerInterface;
use yii\web\IdentityInterface;

/**
 * Class RoleBehavior
 *
 * @package krok\auth\behaviors
 */
class RoleBehavior extends Behavior
{
    /**
     * @var ManagerInterface
     */
    public $authManager = 'authManager';

    /**
     * @var string
     */
    public $attribute = 'roles';

    /**
     * @var IdentityInterface
     */
    public $model;

    public function init()
    {
        $this->authManager = Instance::ensure($this->authManager, ManagerInterface::class);

        parent::init();
    }

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function afterFind()
    {
        $this->model->{$this->attribute} = $this->getRoles();
    }

    public function afterInsert()
    {
        $roles = $this->model->{$this->attribute};
        $this->assign($roles);
    }

    public function afterUpdate()
    {
        $roles = (array)$this->model->{$this->attribute};

        if (is_array($roles)) {
            $current = $this->getRoles();

            $diff = array_diff($roles, $current);
            $this->assign($diff);

            $diff = array_diff($current, $roles);
            $this->revoke($diff);
        }
    }

    public function afterDelete()
    {
        $this->revokeAll($this->model->getId());
    }

    /**
     * @param array $roles
     */
    protected function assign(array $roles)
    {
        if (is_array($roles)) {
            $roles = array_diff($roles, ['']);
            foreach ($roles as $role) {
                $this->authManager->assign($this->authManager->getRole($role), $this->model->getId());
            }
        }
    }

    /**
     * @param array $roles
     */
    protected function revoke(array $roles)
    {
        if (is_array($roles)) {
            $roles = array_diff($roles, ['']);
            foreach ($roles as $role) {
                $this->authManager->revoke($this->authManager->getRole($role), $this->model->getId());
            }
        }
    }

    /**
     * @param string $userId
     */
    protected function revokeAll(string $userId)
    {
        $this->authManager->revokeAll($userId);
    }

    /**
     * @return array
     */
    protected function getRoles(): array
    {
        return ArrayHelper::map($this->authManager->getRolesByUser($this->model->getId()), 'name', 'name');
    }
}
