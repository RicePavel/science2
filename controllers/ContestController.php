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
use app\helpers\StringHelper;
use yii\web\UploadedFile;
use yii\db\Query;
use yii\helpers\Url;

class ContestController extends Controller {
    
    public $enableCsrfValidation = false;
    
    private $FILE_DIR = '../upload/';
    
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
    
    public function actionAdd_test() {
        $myText = $_REQUEST['myText'];
        $myFile = UploadedFile::getInstanceByName('myFile');
    }
    
    public function actionAdd() {
        $ok = true;
        $error = '';
        if (isset($_POST['Contest']) && is_array($_POST['Contest'])) {
            $contestParams = $_POST['Contest'];
            $myFile = UploadedFile::getInstanceByName('report');
            $ok = $this->addContest($contestParams, 'report', $error);
        } else {
            $ok = false;
            $error = 'не переданы параметры';
        }
        $rerult = ['ok' => $ok, 'error' => $error];
        echo BaseJson::encode($rerult);
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
    
    /*
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
     * 
     */
    
    private function changeContest($model, $reportDeleted, &$error) {
        $ok = true;
        if (isset($_POST['Contest'])) {
            $model->setAttributes($_POST['Contest'], false);
            if (!isset($_POST['Contest']['report_exist'])) {
                $model->report_exist = false;
            }
            ContestController::formatToSql($model);
            $ok = $model->save();
            ContestController::formatToWeb($model);
            if (!$ok) {
                $error .= implode(', ', $model->getErrorSummary(true));
                return;
            }
            if ($ok && !$model->report_exist && $model->report_server_name != null) {
                $fileServerName = $model->report_server_name;
                $model->report_name = null;
                $model->report_server_name = null;
                $ok = $model->save();
                if (!$ok) {
                    $error .= implode(', ', $model->getErrorSummary(true));
                    return;
                }
                if ($ok && $fileServerName != null) {
                    $ok = $this->deleteFile($model->contest_id, $fileServerName, $error);
                    if (!$ok) {
                        return $ok;
                    }
                }
            }
            if ($reportDeleted) {
                $fileServerName = $model->report_server_name;
                $model->report_name = null;
                $model->report_server_name = null;
                $ok = $model->save();
                if (!$ok) {
                    $error .= implode(', ', $model->getErrorSummary(true));
                    return;
                }
                if ($ok && $fileServerName != null) {
                    $ok = $this->deleteFile($model->contest_id, $fileServerName, $error);
                    if (!$ok) {
                        return false;
                    }
                }
            }
            $uploadedFile = UploadedFile::getInstanceByName('report');
            if ($model->report_exist && $uploadedFile != null) {
               $ok = $this->saveFile($model, $uploadedFile, $error);
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
    
    public function actionGet_one_json_full() {
       $model = new Contest();
        if (isset($_REQUEST['contest_id'])) {
            $id = $_REQUEST['contest_id'];
            $model = Contest::findOne($id);
        }
        ContestController::formatToWeb($model);
        $teachers = Teachers::find()->all();
        $audiences = Audience::find()->all();
        $locations = Locations::find()->all();
        $fileUrl = '';
        $contestId = (string) $model->contest_id;
        $reportServerName = $model->report_server_name;
        if ($reportServerName != null) {
            $fileUrl = $this->getFileUrl($contestId, $reportServerName);
        }
        $result = ['contest' => $model, 'teachers' => $teachers, 'audiences' => $audiences, 'locations' => $locations, 'fileUrl' => $fileUrl];
        $json = BaseJson::encode($result);
        echo $json; 
    }
    
    public function actionGet_change_form() {
        $model = new Contest();
        if (isset($_REQUEST['contest_id'])) {
            $model = Contest::findOne($_REQUEST['contest_id']);
        }
        ContestController::formatToWeb($model);
        return $this->renderPartial('change_contest_form', ['model' => $model]);
    }
    
    public function actionGet_file() {
        $contestId = $_REQUEST['contest_id'];
        $reportServerName = $_REQUEST['report_server_name'];
        
        $pathToFile = $this->FILE_DIR . $contestId . '/' . $reportServerName;
        
        if (file_exists($pathToFile)) {
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header("Content-Disposition: attachment; filename=" . $reportServerName);
            header("Content-Transfer-Encoding: binary ");
            readfile($pathToFile);
        }
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
                    $addResult = $this->addContest($_POST['Contest'], 'report', $addError);
                } else if ($action == 'delete') {
                    $id = $_POST['contest_id'];
                    $this->deleteContest($id);
                } else if ($action == 'change') {
                    $id = $_POST['Contest']['contest_id'];
                    $changeModel = Contest::findOne($id);
                    $reportDeleted = $_REQUEST['report_deleted'];
                    $changeResult = $this->changeContest($changeModel, $reportDeleted, $changeError);
                }
            }
        }
        
        $contestArray = $this->getContestArray();
        
        return $this->render('contest', ['contestArray' => $contestArray, 'addModel' => $addModel, 'changeModel' => $changeModel, 'addResult' => $addResult, 'addError' => $addError, 'changeResult' => $changeResult, 'changeError' => $changeError]);
    }
    
    public function actionChange() {
        $ok = true;
        $error = '';
        if (isset($_POST['Contest']) && is_array($_POST['Contest'])) {
            $id = $_POST['Contest']['contest_id'];
            $changeModel = Contest::findOne($id);
            $reportDeleted = (isset($_REQUEST['report_deleted']) ? true : false);
            $ok = $this->changeContest($changeModel, $reportDeleted, $error);
        } else {
            $ok = false;
            $error = 'не переданы параметры';
        }
        $result = ['ok' => $ok, 'error' => $error];
        echo BaseJson::encode($result);
    }
    
    public function actionList_json() {
        $contestArray = $this->getContestArray();
        $json = BaseJson::encode($contestArray);
        $res = Yii::$app->getResponse();
        $res->format = \yii\web\Response::FORMAT_JSON;
        $res->data = $json;
        $res->send();
        //echo $json;
    }
    
    public function actionDelete() {
        $error = '';
        $ok = true;
        if (isset($_REQUEST['contest_id'])) {
            $this->deleteContest($_REQUEST['contest_id']);
        } else {
            $ok = false;
            $error = 'не передан ИД';
        }
        $result = ['error' => $error, 'ok' => $ok];
        echo BaseJson::encode($result);
    }
    
    private function getContestArray() {
        /*
        $sql = 'SELECT audience.name as audience_name, locations.name as location_name, teachers.name as teacher_name, teachers.surname as teacher_surname, teachers.middlename as teacher_middlename, contest.contest_id, contest.name, contest.start_date, contest.end_date, contest.count_soh, contest.count_ssuz, contest.count_vuz, contest.geography, contest.report_exist, contest.count_member_perm, contest.count_member_othercity, contest.report_exist, contest.report_name, contest.report_server_name
            FROM contest as contest, audience, locations, teachers 
         WHERE
            contest.audience_id = audience.audience_id
            and contest.location_id = locations.location_id
            and contest.teacher_id = teachers.teacher_id ';
        $contestArray = Yii::$app->db->createCommand($sql)->queryAll();
         * 
         */
        $query = new Query();
        $query->select(['contest.*', 't.name as teacher_name', 't.surname as teacher_surname', 't.middlename as teacher_middlename',
            'audience.name as audience_name', 'locations.name as location_name'])
                ->from(['contest'])
                ->innerJoin('teachers as t', 'contest.teacher_id = t.teacher_id')
                ->innerJoin('audience', 'contest.audience_id = audience.audience_id')
                ->innerJoin('locations', 'contest.location_id = locations.location_id');
        $sorting = isset($_REQUEST['sorting']) ? $_REQUEST['sorting'] : '';
        $sortingType = isset($_REQUEST['sorting_type']) ? $_REQUEST['sorting_type'] : '';
        if ($sorting == 'teacher') {
           $query->orderBy('t.surname' . ' ' . $sortingType); 
        } else if ($sorting == 'audience') {
           $query->orderBy('audience.name' . ' ' . $sortingType); 
        } else if ($sorting == 'location') {
           $query->orderBy('locations.name' . ' ' . $sortingType); 
        } else if ($sorting !== '') {
           $query->orderBy('contest.' . $sorting . ' ' . $sortingType); 
        }
        $contestArray = $query->all();
        foreach ($contestArray as $key => $row) {
            $contestArray[$key]['start_date'] = DateFormat::toWebFormat($row['start_date']);
            $contestArray[$key]['end_date'] = DateFormat::toWebFormat($row['end_date']);
            if ($row['report_server_name'] != null) {
                $contestId = $row['contest_id'];
                $reportServerName = $row['report_server_name'];
                $fileUrl = $this->getFileUrl($contestId, $reportServerName);
                $contestArray[$key]['file_url'] = $fileUrl;
            }
        }
        return $contestArray;
    }
    
    private function getFileUrl($contestId, $reportServerName) {
        return Url::to(['contest/get_file', 'contest_id' => $contestId, 'report_server_name' => $reportServerName]);
    }

    private function deleteContest($id) {
        $contest = Contest::findOne($id);
        $contest->delete();
    }
    
    private function addContest($paramsArray, $fileInputName, &$error) {
       $model = new Contest();
       $model->setAttributes($paramsArray, false);
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
           $file = UploadedFile::getInstanceByName($fileInputName);
           if ($file !== null) {
               $pathToDir = '../upload/' . $model->contest_id;
               if (!file_exists($pathToDir)) {
                   $ok = mkdir($pathToDir);
                   if (!$ok) {
                       $error = 'не удалось создать директорию для хранения файла';
                       return false;
                   }
               }
               $originalName = $file->name;
               $translitName = StringHelper::translit($originalName);
               $pathToFile = $pathToDir . '/' . $translitName;
               $ok = $file->saveAs($pathToFile);
               if (!$ok) {
                   $error .= 'не удалось сохранить файл';
                   return false;
               }
               $model->report_name = $originalName;
               $model->report_server_name = $translitName;
               $ok = $model->save();
               if (!$ok) {
                   $error .= implode(', ', $model->getErrorSummary(true));
               }
           }
       }
       return $ok;
    }
    
    private function addContest_1(&$error) {
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
               $pathToDir = '../upload/' . $model->contest_id;
               if (!file_exists($pathToDir)) {
                   $ok = mkdir($pathToDir);
                   if (!$ok) {
                       $error = 'не удалось создать директорию для хранения файла';
                       return false;
                   }
               }
               $originalName = $file->name;
               $translitName = StringHelper::translit($originalName);
               $pathToFile = $pathToDir . '/' . $translitName;
               $ok = $file->saveAs($pathToFile);
               if (!$ok) {
                   $error .= 'не удалось сохранить файл';
                   return false;
               }
               $model->report_name = $originalName;
               $model->report_server_name = $translitName;
               $ok = $model->save();
               if (!$ok) {
                   $error .= implode(', ', $model->getErrorSummary(true));
               }
           }
       }
       return $ok;
    }
    
    private function deleteFile($contestId, $fileServerName, &$error) {
        $ok = true;
        if ($fileServerName != null) {     
            $pathToFile = $this->FILE_DIR . $contestId . '/' . $fileServerName;
            if (file_exists($pathToFile)) {
                $ok = unlink($pathToFile);
                if (!$ok) {
                    $error = 'не удалось удалить файл';
                    return $ok;
                }
            } else {
                $error = 'файл не найден';
                return false;
            }           
        } else {
            $error = 'не задано имя файла';
            return false;
        }
        return $ok;
    }
    
    private function saveFile($model, $uploadedFile, &$error) {
        if ($uploadedFile !== null) {
               $ok = true;
               $pathToDir = $this->FILE_DIR . $model->contest_id;
               if (!file_exists($pathToDir)) {
                   $ok = mkdir($pathToDir);
                   if (!$ok) {
                       $error = 'не удалось создать директорию для хранения файла';
                       return $ok;
                   }
               }
               $originalName = $uploadedFile->name;
               $translitName = StringHelper::translit($originalName);
               $pathToFile = $pathToDir . '/' . $translitName;
               $ok = $uploadedFile->saveAs($pathToFile);
               if (!$ok) {
                   $error .= 'не удалось сохранить файл';
                   return $ok;
               }
               $model->report_name = $originalName;
               $model->report_server_name = $translitName;
               $ok = $model->save();
               if (!$ok) {
                   $error = implode(', ', $model->getErrorSummary(true));
               }
               return $ok;
        } else {
            $error = 'не загружен файл';
            return false;
        }
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

