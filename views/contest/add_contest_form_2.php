

<script>
        $(function() {
            $('.dateInput').datepicker({dateFormat: 'dd.mm.yy'});
        });
</script>

<div>
    
    <?php 
    
    use yii\helpers\Html;
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
    
    $options = ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data', 'id' => 'addContestForm'];

    echo Html::beginForm('', '', $options);
            
    ?>
    
        <?php 
        if ($addResult === false) {
        ?>
        <div class="alert alert-danger" role="alert"> 
            <?= $addError ?>
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
                    <input type="text" name="" class="form-control locationTextInput" required />
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
                <input type="hidden" name="Contest[location_id]" value="" class="locationIdHiddenInput"  />
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
            <label class="col-md-3 control-label" >Файл</label>
            <div class="col-md-8"> 
                <input type="checkbox" value="1" class="contestFileCheckbox" name="Contest[report_exist]" />
                <input type="file" style="display: none;" class="contestFileInput" name="report" />
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

                echo Html::hiddenInput('action', 'add');
   
        ?>
    
        <div class="form-group">
            <div class="col-md-offset-3 col-md-8">
                <input type="submit" class="btn btn-primary" name="submit" value="Сохранить" />
            </div>
        </div>
    
    <?= Html::endForm() ?>
    
</div>

