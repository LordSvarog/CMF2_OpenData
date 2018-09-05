<?php

namespace krok\cabinet\controllers\backend;

use krok\cabinet\models\Client;
use krok\cabinet\models\LogSearch;
use krok\system\components\backend\Controller;
use Yii;

/**
 * LogController implements the CRUD actions for Log model.
 */
class LogController extends Controller
{
    /**
     * Lists all Log models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $clients = Yii::createObject(Client::class)::find()->asDropDown();
        $searchModel = Yii::createObject(LogSearch::class);
        $dataProvider = $searchModel->search();

        return $this->render('index', [
            'clients' => $clients,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
