<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Audience;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Locations;
use app\models\Teachers;
use app\models\Contest;
use yii\helpers\BaseJson;
use app\helpers\DateFormat;
use yii\web\UploadedFile;

class ContestController extends Controller {
    
    public $enableCsrfValidation = false;
    
     public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }
    
    public function actionChangecontest() {
        
        $teachers = Teachers::find()->all();
        $audiences = Audience::find()->all();
        $locations = Locations::find()->all();
        
        if (isset($_POST['contest_id'])) {
            $id = $_POST['contest_id'];
            
            $error = '';
            $model = Contest::findOne($id);
            changeContest($model, $error);
            
            return $this->render('changecontest', ['model' => $model, 'locations' => $locations, 'audiences' => $audiences, 'teachers' => $teachers]);
        }
    }
    
    private function changeContest($model, &$error) {
        $ok = true;
        if (isset($_POST['Contest'])) {
            $model->setAttributes($_POST['Contest'], false);
            ContestController::formatToSql($model);
            $ok = $model->save();
            ContestController::formatToWeb($model);
            if (!$ok) {
                $error .= implode(', ', $model->getErrorSummary(true));
            }
        } else {
            $ok = false;
            $error = 'не заданы параметры';
        }
        return $ok;
    }
    
    public function actionGet_one_json() {
        $model = new Contest();
        if (isset($_REQUEST['contest_id'])) {
            $id = $_REQUEST['contest_id'];
            $model = Contest::findOne($id);
        }
        ContestController::formatToWeb($model);
        $json = BaseJson::encode($model);
        echo $json;
    }
    
    public function actionList() {    
        $request = Yii::$app->request;
        
        $addModel = new Contest();
        $changeModel = new Contest();
        $addError = '';
        $addResult = true;
        $changeError = '';
        $changeResult = true;
        if (isset($_POST['submit'])) {
            if (isset($_POST['action'])) {
                $action = $_POST['action'];
                if ($action == 'add') {
                    $addResult = $this->addContest($addError);
                } else if ($action == 'delete') {
                    $id = $_POST['contest_id'];
                    $this->deleteContest($id);
                } else if ($action == 'change') {
                    $id = $_POST['Contest']['contest_id'];
                    $changeModel = Contest::findOne($id);
                    $changeResult = $this->changeContest($changeModel, $changeError);
                }
            }
        }
        
        $sql = 'SELECT audience.name as audience_name, locations.name as location_name, teachers.name as teacher_name, teachers.surname as teacher_surname, teachers.middlename as teacher_middlename, contest.contest_id, contest.name, contest.start_date, contest.end_date, contest.count_soh, contest.count_ssuz, contest.count_vuz, contest.geography, contest.report_exist, contest.count_member_perm, contest.count_member_othercity
            FROM contest as contest, audience, locations, teachers 
         WHERE
            contest.audience_id = audience.audience_id
            and contest.location_id = locations.location_id
            and contest.teacher_id = teachers.teacher_id ';
        $contestArray = Yii::$app->db->createCommand($sql)->queryAll();
        
        return $this->render('contest', ['contestArray' => $contestArray, 'addModel' => $addModel, 'changeModel' => $changeModel, 'addResult' => $addResult, 'addError' => $addError, 'changeResult' => $changeResult, 'changeError' => $changeError]);
    }

    private function deleteContest($id) {
        $contest = Contest::findOne($id);
        $contest->delete();
    }
    
    private function addContest(&$error) {
        
       $model = new Contest();
       $model->setAttributes($_POST['Contest'], false);
       ContestController::formatToSql($model);
       $ok = $model->save();
       ContestController::formatToWeb($model);
       if (!$ok) {
           $errorsArray = $model->errors;
           foreach ($errorsArray as $secondArray) {
               $error .= implode(', ', $secondArray);
           }
       }
       if ($ok && $model->report_exist == true) {
           $file = UploadedFile::getInstanceByName('report');
           if ($file !== null) {
               /*
               $path = '../upload/' . $model->contest_id . '/' . $file->name;
               $ok = $file->saveAs($path);
               if (!$ok) {
                   $error .= 'не удалось сохранить файл';
               }
               */
           }
       }
       return $ok;
    }
        
    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
    
    public static function formatToSql($model) {
        $model->start_date = DateFormat::toSqlFormat($model->start_date);
        $model->end_date = DateFormat::toSqlFormat($model->end_date);
    }
    
    public static function formatToWeb($model) {
        $model->start_date = DateFormat::toWebFormat($model->start_date);
        $model->end_date = DateFormat::toWebFormat($model->end_date);
    }
  
    
}

