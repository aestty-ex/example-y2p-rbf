<?php

namespace app\controllers;

use Yii;
use app\models\Account;
use app\models\Operation;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OperationController implements the CRUD actions for Operation model.
 */
class OperationController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Operation models.
     * @param integer $accountId
     * @return mixed
     */
    public function actionIndex($accountId)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Operation::find()
                ->where(['source_account_id' => $accountId])
                ->orWhere(['target_account_id' => $accountId, 'status' => Operation::STATUS_COMPLETED])
        ]);

        return $this->render('index', [
            'accountId' => $accountId,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Operation model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Operation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param integer $accountId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionCreate($accountId)
    {
        $model = new Operation();
        if (($account = Account::findOne($accountId)) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $model->source_account_id = $account->id;
        $balance = Account::getPlannedBalance($account);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'balance' => $balance,
            'model' => $model,
        ]);
    }

    /**
     * Finds the Operation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Operation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Operation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
