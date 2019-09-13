<?php
/**
 * Created by PhpStorm.
 * User: elfuvo
 * Date: 08.08.17
 * Time: 17:56
 */

namespace app\modules\opendata\forms;

use yii\base\Model;

/**
 * Class CommentForm
 *
 * @package app\modules\opendata\forms
 */
class CommentForm extends Model
{
    /**
     * @var string
     */
    public $name = '';

    /**
     * @var string
     */
    public $email = '';

    /**
     * @var string
     */
    public $comment = '';

    /**
     * @var null
     */
    public $verifyCode = null;

    /**
     * @var int
     */
    public $deal;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'email', 'comment', 'deal'], 'required'],
            [['name'], 'string'],
            ['email', 'email'],
            [['comment'], 'string', 'max' => 3728],
            ['verifyCode', 'captcha', 'captchaAction' => 'opendata/default/captcha'],
            [
                [
                    'deal',
                ],
                'required',
                'message' => 'Подтвердите, что ознакомлены с порядком приема обращения',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'ФИО посетителя *',
            'email' => 'Email *',
            'comment' => 'Комментарий...',
            'verifyCode' => 'Введите код с картинки',
            'deal' => 'Разрешаю обработку персональных данных',
        ];
    }
}
