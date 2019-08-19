<?php
/**
 * Created by PhpStorm.
 * User: krok
 * Date: 19.08.19
 * Time: 14:58
 */

namespace krok\auth\grid;

use krok\auth\models\Log;
use krok\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class AuthorizedListColumn
 *
 * @package krok\auth\grid
 */
class AuthorizedListColumn extends DataColumn
{
    /**
     * @var string
     */
    public $attribute;

    /**
     * @var string
     */
    public $format = 'raw';

    /**
     * @param mixed $model
     * @param mixed $key
     * @param int $index
     *
     * @return string
     */
    public function getDataCellValue($model, $key, $index)
    {
        if ($this->value === null) {
            if ($model instanceof Log) {
                if ($model->authRelation) {
                    return Html::a($model->authRelation->login, ['auth/view', 'id' => $model->authRelation->id],
                        ['target' => '_blank']);
                } else {
                    return null;
                }
            }
        }

        return parent::getDataCellValue($model, $key, $index);
    }

    /**
     * @return string
     */
    protected function renderFilterCellContent()
    {
        if ($this->filter === null) {
            $models = Log::find()->joinWith('authRelation')->where([
                'IS NOT',
                'authId',
                null,
            ])->distinct()->asArray()->all();

            $this->filter = ArrayHelper::map($models,
                function (array $model) {
                    return ArrayHelper::getValue($model, ['authRelation', 'id']);
                }, function (array $model) {
                    return ArrayHelper::getValue($model, ['authRelation', 'login']);
                });
        }

        return parent::renderFilterCellContent();
    }
}
