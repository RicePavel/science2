

<script>
        $(function() {
            $('.dateInput').datepicker({dateFormat: 'dd.mm.yy'});
        });
</script>

<div>
    
    <?php 
    
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\helpers\ArrayHelper;
    use yii\widgets\ActiveForm;
    use app\models\Teachers;
    use app\models\Audience;
    use app\models\Locations;
    
    ?>

    <?php
    
    $teachers = Teachers::find()->all();   
    $audiences = Audience::find()->all();
    $locations = Locations::find()->all();
    
    $teachersArray = [];
    foreach ($teachers as $teacher) {
        $teachersArray[$teacher->teacher_id] = $teacher->surname . ' ' . $teacher->name . ' ' . $teacher->middlename;
    }
    
    $audienceArray = [];
    foreach ($audiences as $audience) {
        $audiencesArray[$audience->audience_id] = $audience->name;
    }
    
    $locationsArray = [];
    foreach ($locations as $location) {
        $locationsArray[$location->location_id] = $location->name;
    }
    
    ?>
    
    <?php
    
        $options = ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data', 'id' => 'changeContestForm'];
        $action = Url::to('?r=contest/list');
        echo Html::beginForm($action, '', $options);
        
    ?>
    
        <?php 
            if (isset($changeResult) && $changeResult === false) {
            ?>
            <div class="alert alert-danger" role="alert"> 
                <?= $changeError ?>
            </div>
        <?php } ?>
    
        <div class="form-group">
            <label class="col-md-3 control-label">Преподаватель</label>
            <div class="col-md-8">
                <?= Html::beginTag('select', ['name' => 'Contest[teacher_id]', 'class' => 'form-control']) ?>
                <?= Html::renderSelectOptions($model->teacher_id, $teachersArray) ?>  
                <?= Html::endTag('select') ?>
            </div>
        </div>
    
        <div class="form-group">
            <label class="col-md-3 control-label">Целевая аудитория</label>
            <div class="col-md-8">
                <?= Html::beginTag('select', ['name' => 'Contest[audience_id]', 'class' => 'form-control']) ?>
                <?= Html::renderSelectOptions($model->audience_id, $audiencesArray) ?>  
                <?= Html::endTag('select') ?>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label">Название</label>
            <div class="col-md-8">
                <?= Html::activeTextarea($model, 'name', ['class' => 'form-control' /*, 'required' => ''*/]) ?>
            </div>
        </div>
        
        <div class="form-group locationFormGroup">
            <label class="col-md-3 control-label">Место проведения</label> 
            <div class="col-md-8">
                <div class="input-group">
                <?php
                    $locationName = '';
                    foreach ($locations as $location) {
                        if ($location->location_id == $model->location_id) {
                            $locationName = $location->name;
                            break;
                        }
                    }  
                    ?>
                    <input type="text" name="" class="form-control locationTextInput" value="<?= $locationName ?>" required />
                    <span class="input-group-btn">
                        <button class="btn btn-default locationRemoveButton" type="button" style="border-left:none;">
                           <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> 
                        </button>
                        <button class="btn btn-default locationDownButton" type="button">
                            <span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span>
                        </button>
                    </span>
                </div>
                <ul class="locationList">
                    <?php foreach ($locations as $location) { ?>
                    <li class="locationListElement" data-id="<?= $location->location_id ?>"><?= $location->name ?></li>
                    <?php } ?>
                </ul>
                <input type="hidden" name="Contest[location_id]" value="<?= $model->location_id ?>" class="locationIdHiddenInput"  />
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label">Дата начала</label>
            <div class="col-md-8">
                <input type="text" name="Contest[start_date]" value="<?= $model->start_date ?>" class="form-control dateInput" />
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label">Дата окончания</label>
            <div class="col-md-8">
                <input type="text" name="Contest[end_date]" value="<?= $model->end_date ?>" class="form-control dateInput" />
            </div>
        </div>
   
        <div class="form-group">
            <label class="col-md-3 control-label">СОШ</label>
            <div class="col-md-8">
                <?= Html::activeInput('text', $model, 'count_soh', ['class' => 'form-control']) ?>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label">ССУЗ</label>
            <div class="col-md-8">
                <?= Html::activeInput('text', $model, 'count_ssuz', ['class' => 'form-control']) ?>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label">ВУЗ</label>
            <div class="col-md-8">
                <?= Html::activeInput('text', $model, 'count_vuz', ['class' => 'form-control']) ?>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label">Из Перми</label>
            <div class="col-md-8">
                <?= Html::activeInput('text', $model, 'count_member_perm', ['class' => 'form-control']) ?>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label">Иногородних</label>
            <div class="col-md-8">
                <?= Html::activeInput('text', $model, 'count_member_othercity', ['class' => 'form-control']) ?>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label">География участников</label>
            <div class="col-md-8">
                <?= Html::activeInput('text', $model, 'geography', ['class' => 'form-control']) ?>
            </div>
        </div>
    
        <div class="form-group">
            <!-- сюда записывается инфа для js о том, есть ли файл на сервере -->
            <input type="hidden" id="fileOnServerExist" value="<?= ($model->report_server_name != null) ? '1' : '' ?>" />
            
            <input type="hidden" name="report_deleted" value="" />
            
            <label class="col-md-3 control-label" >Файл</label>
            <div class="col-md-8"> 
                <input type="checkbox" class="contestFileCheckbox" value="1" name="Contest[report_exist]" <?= ($model->report_exist) ? 'checked' : '' ?> />
                <?php 
                    $showContestFileInput = false;
                    if ($model->report_exist && $model->report_name == null) {
                        $showContestFileInput = true;
                    }
                ?>
                <input type="file" style="<?= ($showContestFileInput) ? '' : 'display: none;' ?>" class="contestFileInput" name="report" /> <br/>
                <?php 
                if ($model->report_name != null) {
                    $fileUrl = Url::to(['contest/get_file', 'contest_id' => $model->contest_id, 'report_server_name' => $model->report_server_name]);
                ?>
                <div class="contestFileLinkContainer">
                    <a class="contestFileLink" href="<?= $fileUrl ?>"><?= $model->report_name ?></a> &nbsp; &nbsp;
                    <a href="#" onclick="clickFileDeleteLink()" > <img src="img/delete.png" style="width:20px;"/> </a>
                </div>
                <?php 
                } 
                ?>
            </div>
        </div>
    
        <?php
            if ($model->contest_id) {
                echo Html::hiddenInput('Contest[contest_id]', $model->contest_id, []);
            } else {
                echo Html::hiddenInput('Contest[contest_id]', '', []);
            }
        ?>
        
        <?php

            echo Html::hiddenInput('action', 'change');

        ?>
    
        <div class="form-group">
            <div class="col-md-offset-3 col-md-8">
                <input type="submit" class="btn btn-primary" name="submit" value="Сохранить" />
            </div>
        </div>
    
    <?= Html::endForm() ?>
    
</div>

