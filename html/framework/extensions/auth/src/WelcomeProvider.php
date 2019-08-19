<?php
/**
 * Created by PhpStorm.
 * User: krok
 * Date: 19.08.19
 * Time: 15:19
 */

namespace krok\auth;

use DateTime;
use krok\auth\models\Log;
use krok\paperdashboard\widgets\welcome\WelcomeProviderInterface;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/**
 * Class WelcomeProvider
 *
 * @package krok\auth
 */
class WelcomeProvider implements WelcomeProviderInterface
{
    /**
     * @return string
     */
    public function getLogin(): string
    {
        return ArrayHelper::getValue(Yii::$app->getUser()->getIdentity(), 'login', '');
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return Url::to(['/auth/profile']);
    }

    /**
     * @return string
     */
    public function getLastLoginAt(): string
    {
        $last = Log::find()->where([
            '[[authId]]' => $this->getUserId(),
            '[[status]]' => Log::STATUS_LOGGED,
        ])->orderBy([
            '[[createdAt]]' => SORT_DESC,
        ])->limit(2)->asArray()->all();

        $dt = new DateTime($last['1']['createdAt'] ?? $last['0']['createdAt'] ?? 'now');

        return $this->formatLastLoginAt($dt);
    }

    /**
     * @return string
     */
    protected function getUserId(): string
    {
        return Yii::$app->getUser()->getIdentity()->getId();
    }

    /**
     * @param DateTime $dt
     *
     * @return string
     */
    protected function formatLastLoginAt(DateTime $dt): string
    {
        return Yii::$app->getFormatter()->asDatetime($dt, 'dd MMMM YYYY г., HH:mm');
    }
}
