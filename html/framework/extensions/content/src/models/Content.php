<?php

namespace krok\content\models;

use krok\content\MetaConfigurableAdapter;
use krok\content\Module;
use krok\content\OpenGraphConfigurableAdapter;
use krok\datetimeFormatter\validators\DatetimeFormatterValidator;
use krok\extend\behaviors\LanguageBehavior;
use krok\extend\behaviors\TagDependencyBehavior;
use krok\grid\interfaces\HiddenAttributeInterface;
use krok\grid\traits\HiddenAttributeTrait;
use krok\meta\behaviors\MetaBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%content}}".
 *
 * @property integer $id
 * @property string $alias
 * @property string $title
 * @property string $text
 * @property string $layout
 * @property string $view
 * @property integer $hidden
 * @property string $language
 * @property string $createdAt
 * @property string $updatedAt
 */
class Content extends \yii\db\ActiveRecord implements HiddenAttributeInterface
{
    use HiddenAttributeTrait;

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
            'TagDependencyBehavior' => [
                'class' => TagDependencyBehavior::class,
            ],
            'LanguageBehavior' => [
                'class' => LanguageBehavior::class,
            ],
            'MetaBehavior' => [
                'class' => MetaBehavior::class,
                'adapters' => [
                    MetaConfigurableAdapter::class,
                    OpenGraphConfigurableAdapter::class,
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%content}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['alias', 'title', 'text', 'layout', 'view', 'hidden'], 'required'],
            [['text'], 'string'],
            [['hidden'], 'integer'],
            [['createdAt', 'updatedAt'], 'safe'],
            [['createdAt', 'updatedAt'], DatetimeFormatterValidator::class],
            [['alias'], 'match', 'pattern' => '/^([a-z\-\_0-9]+)$/i'],
            [['alias'], 'string', 'min' => 2, 'max' => 256],
            [['layout', 'view'], 'string', 'max' => 64],
            [['title'], 'string', 'max' => 128],
            [['language'], 'string', 'max' => 8],
            [
                ['alias'],
                'unique',
                'targetAttribute' => ['alias', 'language'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'alias' => 'Псевдоним',
            'title' => 'Заголовок',
            'text' => 'Текст',
            'layout' => 'Макет',
            'view' => 'Шаблон',
            'hidden' => 'Скрыта',
            'language' => 'Язык',
            'createdAt' => 'Дата создания',
            'updatedAt' => 'Дата изменения',
        ];
    }

    /**
     * @return array
     */
    public static function getLayouts(): array
    {
        if (($module = self::getModule()) !== null) {
            return $module->layouts;
        }

        return [];
    }

    /**
     * @return null|string
     */
    public function getLayout(): ?string
    {
        return ArrayHelper::getValue(self::getLayouts(), $this->layout);
    }

    /**
     * @return array
     */
    public static function getViews(): array
    {
        if (($module = self::getModule()) !== null) {
            return $module->views;
        }

        return [];
    }

    /**
     * @return null|string
     */
    public function getView(): ?string
    {
        return ArrayHelper::getValue(self::getViews(), $this->view);
    }

    /**
     * @return Module|null
     */
    public static function getModule(): ?Module
    {
        /** @var Module $module */
        $module = Module::getInstance();

        return $module;
    }

    /**
     * @inheritdoc
     * @return ContentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ContentQuery(get_called_class());
    }
}
