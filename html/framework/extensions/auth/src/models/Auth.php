<?php

namespace krok\auth\models;

use krok\auth\behaviors\RoleBehavior;
use krok\extend\behaviors\GenerateRandomStringBehavior;
use krok\extend\behaviors\HashBehavior;
use krok\extend\behaviors\TimestampBehavior;
use krok\grid\interfaces\BlockedAttributeInterface;
use krok\grid\traits\BlockedAttributeTrait;
use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%auth}}".
 *
 * @property integer $id
 * @property string $login
 * @property string $password
 * @property string $authKey
 * @property string $accessToken
 * @property string $email
 * @property integer $blocked
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property string[] $roles
 * @property Log[] $logs
 */
class Auth extends \yii\db\ActiveRecord implements IdentityInterface, BlockedAttributeInterface
{
    use BlockedAttributeTrait;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_REFRESH_TOKEN = 'refresh_token';

    /**
     * @var array
     */
    private $roles = [];

    /**
     * @return array
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'HashBehaviorPassword' => [
                'class' => HashBehavior::class,
                'attribute' => 'password',
                'scenarios' => [
                    self::SCENARIO_DEFAULT,
                    self::SCENARIO_CREATE,
                ],
            ],
            'GenerateRandomStringBehaviorAuthKey' => [
                'class' => GenerateRandomStringBehavior::class,
                'skipUpdateOnClean' => false,
                'attribute' => 'authKey',
                'stringLength' => 128,
                'scenarios' => [
                    self::SCENARIO_CREATE,
                    self::SCENARIO_REFRESH_TOKEN,
                ],
            ],
            'GenerateRandomStringBehaviorAccessToken' => [
                'class' => GenerateRandomStringBehavior::class,
                'skipUpdateOnClean' => false,
                'attribute' => 'accessToken',
                'stringLength' => 128,
                'scenarios' => [
                    self::SCENARIO_CREATE,
                    self::SCENARIO_REFRESH_TOKEN,
                ],
            ],
            'TimestampBehavior' => [
                'class' => TimestampBehavior::class,
            ],
            'RoleBehavior' => [
                'class' => RoleBehavior::class,
                'model' => $this,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['blocked'], 'integer'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['login'], 'string', 'max' => 32, 'min' => 4],
            [['password'], 'string', 'max' => 512, 'min' => 8],
            [['email'], 'string', 'max' => 64],
            [
                ['authKey', 'accessToken'],
                'string',
                'max' => 128,
                'on' => [
                    self::SCENARIO_CREATE,
                    self::SCENARIO_REFRESH_TOKEN,
                ],
            ],
            [
                ['authKey', 'accessToken'],
                'unique',
                'on' => [
                    self::SCENARIO_CREATE,
                    self::SCENARIO_REFRESH_TOKEN,
                ],
            ],
            [['login'], 'unique'],
            [['login'], 'required'],
            [['email'], 'email'],
            [['password'], 'required', 'on' => [self::SCENARIO_CREATE]],
            /**
             * virtual property
             */
            [['roles'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Логин',
            'password' => 'Пароль',
            'roles' => 'Роли',
            'authKey' => 'Ключ авторизации',
            'accessToken' => 'Токен доступа',
            'email' => 'Электронная почта',
            'blocked' => 'Заблокирован',
            'createdAt' => 'Создано',
            'updatedAt' => 'Обновлено',
        ];
    }

    /**
     * @param string $password
     *
     * @return bool
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    /**
     * @param int|string $id
     *
     * @return IdentityInterface|static
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'blocked' => self::BLOCKED_NO]);
    }

    /**
     * @param mixed $token
     * @param null $type
     *
     * @return IdentityInterface|static
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['accessToken' => $token, 'blocked' => self::BLOCKED_NO]);
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @param string $authKey
     *
     * @return bool
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLogs()
    {
        return $this->hasMany(Log::class, ['authId' => 'id']);
    }

    /**
     * @inheritdoc
     * @return AuthQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AuthQuery(get_called_class());
    }
}
