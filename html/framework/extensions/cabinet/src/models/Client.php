<?php

namespace krok\cabinet\models;

use krok\cabinet\interfaces\LoginAsInterface;
use krok\extend\behaviors\GenerateRandomStringBehavior;
use krok\extend\behaviors\HashBehavior;
use krok\extend\behaviors\TagDependencyBehavior;
use krok\extend\behaviors\TimestampBehavior;
use krok\extend\interfaces\BlockedAttributeInterface;
use krok\extend\traits\BlockedAttributeTrait;
use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%client}}".
 *
 * @property integer $id
 * @property string $login
 * @property string $password
 * @property string $authKey
 * @property string $accessToken
 * @property integer $blocked
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property Log[] $clientLogsRelation
 * @property OAuth[] $clientOAuthRelation
 */
class Client extends \yii\db\ActiveRecord implements IdentityInterface, LoginAsInterface, BlockedAttributeInterface
{
    use BlockedAttributeTrait;

    const SCENARIO_CREATE = 'create';
    const SCENARIO_REFRESH_TOKEN = 'refresh_token';

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
            'TimestampBehavior' => [
                'class' => TimestampBehavior::class,
            ],
            'HashBehavior' => [
                'class' => HashBehavior::class,
                'attribute' => 'password',
                'scenarios' => [
                    self::SCENARIO_DEFAULT,
                    self::SCENARIO_CREATE,
                ],
            ],
            'AuthKeyGenerateRandomStringBehavior' => [
                'class' => GenerateRandomStringBehavior::class,
                'skipUpdateOnClean' => false,
                'attribute' => 'authKey',
                'stringLength' => 128,
                'scenarios' => [
                    self::SCENARIO_CREATE,
                    self::SCENARIO_REFRESH_TOKEN,
                ],
            ],
            'AccessTokenGenerateRandomStringBehavior' => [
                'class' => GenerateRandomStringBehavior::class,
                'skipUpdateOnClean' => false,
                'attribute' => 'accessToken',
                'stringLength' => 128,
                'scenarios' => [
                    self::SCENARIO_CREATE,
                    self::SCENARIO_REFRESH_TOKEN,
                ],
            ],
            'TagDependencyBehavior' => [
                'class' => TagDependencyBehavior::class,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%client}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['blocked'], 'integer'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['login'], 'string', 'max' => 64, 'min' => 4],
            [['password'], 'string', 'max' => 60, 'min' => 8],
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
            [['password'], 'required', 'on' => [self::SCENARIO_CREATE]],
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
            'authKey' => 'Ключ авторизации',
            'accessToken' => 'Маркер доступа',
            'blocked' => 'Заблокирован',
            'createdAt' => 'Создано',
            'updatedAt' => 'Обновлено',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientLogsRelation()
    {
        return $this->hasMany(Log::className(), ['clientId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientOAuthRelation()
    {
        return $this->hasMany(OAuth::className(), ['clientId' => 'id']);
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
     * @return mixed|string
     */
    public function loginAs()
    {
        return Yii::$app->getUrlManager('frontend')->createUrl(['/cabinet/auth', 'access-token' => $this->accessToken]);
    }

    /**
     * @inheritdoc
     * @return ClientQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ClientQuery(get_called_class());
    }
}
