<?php
/**
 * Created by PhpStorm.
 * User: krok
 * Date: 09.08.18
 * Time: 14:55
 */

namespace krok\auth;

use krok\configure\types\DropDownType;
use Yii;

/**
 * Class Configurable
 *
 * @package krok\auth
 */
class Configurable extends \krok\configure\Configurable
{
    const USE_CAPTCHA_NO = 0;
    const USE_CAPTCHA_YES = 1;

    const AUTH_TIMEOUT = 1 * 60 * 60;

    /**
     * @var int
     */
    public $useCaptcha = self::USE_CAPTCHA_YES;

    /**
     * @var int seconds
     */
    public $authTimeout = self::AUTH_TIMEOUT;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['useCaptcha'], 'integer'],
            [['authTimeout'], 'integer', 'min' => 1, 'max' => Yii::$app->getSession()->timeout / 60],
            [['useCaptcha', 'authTimeout'], 'required'],
            [
                ['authTimeout'],
                function ($attribute) {
                    $this->{$attribute} *= 60;
                },
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'useCaptcha' => 'Проверочный код',
            'authTimeout' => 'Время сессии',
        ];
    }

    /**
     * @return array
     */
    public function attributeHints()
    {
        return [
            'authTimeout' => 'Бездействие в минутах по истечению которых нужно авторизоваться повторно',
        ];
    }

    /**
     * @return string
     */
    public static function label(): string
    {
        return 'Настройки авторизации';
    }

    /**
     * @return array
     */
    public static function attributeTypes(): array
    {
        return [
            'useCaptcha' => [
                'class' => DropDownType::class,
                'config' => [
                    'items' => static::getUseCaptchaList(),
                ],
            ],
            'authTimeout' => [
                'class' => AuthTimeoutType::class,
            ],
        ];
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function populate(array $data): bool
    {
        if ($this->load($data)) {
            return $this->validate();
        }

        return false;
    }

    /**
     * @return array
     */
    public static function getUseCaptchaList(): array
    {
        return [
            static::USE_CAPTCHA_NO => 'Никогда не проверять',
            static::USE_CAPTCHA_YES => 'Проверять всегда',
        ];
    }
}
