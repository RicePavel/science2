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
use app\models\ContestOrganisation;

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
        
        if (isset($_POST['contest_organisation_id'])) {
            $id = $_POST['contest_organisation_id'];
            $model = ContestOrganisation::findOne($id);
            
            if (isset($_POST['ContestOrganisation'])) {
                $model->setAttributes($_POST['ContestOrganisation'], false);
                $model->start_date = $this->dateToSqlFormat($model->start_date);
                $model->end_date = $this->dateToSqlFormat($model->end_date);
                $model->save();
            }
            
            $model->start_date = $this->dateFromSqlFormat($model->start_date);
            $model->end_date = $this->dateFromSqlFormat($model->end_date);
            return $this->render('changecontest', ['model' => $model, 'locations' => $locations, 'audiences' => $audiences, 'teachers' => $teachers]);
        }
    }
    
    public function actionList() {
         
        $request = Yii::$app->request;
        /*
        if ($request->$isPost) {
            $parametersArray = $request->post();
        }
         */
        if (isset($_POST['submit'])) {
            if (isset($_POST['action'])) {
                $action = $_POST['action'];
                if ($action == 'add') {
                    $this->addContest();
                } else if ($action == 'delete') {
                    $id = $_POST['contest_organisation_id'];
                    $this->deleteContest($id);
                }
            }
        }
        
        $sql = 'SELECT audience.name as audience_name, locations.name as location_name, teachers.name as teacher_name, teachers.surname as teacher_surname, teachers.middlename as teacher_middlename, contest.contest_organisation_id, contest.name, contest.start_date, contest.end_date, contest.count_soh, contest.count_ssuz, contest.count_vuz, contest.geography, contest.report_exist, contest.count_member_perm, contest.count_member_othercity
            FROM contest_organisation as contest, audience, locations, teachers 
         WHERE
            contest.audience_id = audience.audience_id
            and contest.location_id = locations.location_id
            and contest.teacher_id = teachers.teacher_id ';
        $contestArray = Yii::$app->db->createCommand($sql)->queryAll();
        
        $model = new ContestOrganisation();
        
        return $this->render('contest', ['contestArray' => $contestArray, 'model' => $model]);
    }

    private function deleteContest($id) {
        $contest = ContestOrganisation::findOne($id);
        $contest->delete();
    }
    
    private function addContest() {
       $contest = new ContestOrganisation();
       $contest->setAttributes($_POST['ContestOrganisation'], false);
       $contest->start_date = $this->dateToSqlFormat($contest->start_date);
       $contest->end_date = $this->dateToSqlFormat($contest->end_date);
       $contest->save();
    }
    
    private function addContest_old() {
        $contest = new ContestOrganisation();
        $contest->name = $_POST['name'];
        $contest->teacher_id = $_POST['teacher_id'];
        $contest->audience_id = $_POST['audience_id'];
        $contest->location_id = $_POST['location_id'];
        $contest->start_date = $this->dateToSqlFormat($_POST['start_date']);
        $contest->end_date = $this->dateToSqlFormat($_POST['end_date']);
        
        $contest->count_soh = $_POST['count_soh'];
        $contest->count_ssuz = $_POST['count_ssuz'];
        $contest->count_vuz = $_POST['count_vuz'];
        $contest->count_member_perm = $_POST['count_member_perm'];
        $contest->count_member_othercity = $_POST['count_member_othercity'];
        $contest->geography = $_POST['geography'];
        $contest->save();
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
    
    
    private function dateToSqlFormat($dateString) {
        $arr = explode('.', $dateString);
        $result = '';
        if (count($arr) == 3) {
            $result = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
        }
        return $result;
    }
    
    private function dateFromSqlFormat($dateString) {
        $arr = explode('-', $dateString);
        $result = '';
        if (count($arr) == 3) {
            $result = $arr[2] . '.' . $arr[1] . '.' . $arr[0];
        }
        return $result;
    }
    
}

