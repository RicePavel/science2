

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
    
    <?= Html::beginForm('', '', ['class' => 'form-horizontal']) ?>
    
        <div class="form-group">
            <label class="col-md-3 control-label">Преподаватель</label>
            <div class="col-md-8">
                <?= Html::beginTag('select', [name => 'ContestOrganisation[teacher_id]', 'class' => 'form-control']) ?>
                <?= Html::renderSelectOptions($model->teacher_id, $teachersArray) ?>  
                <?= Html::endTag('select') ?>
            </div>
        </div>
    
        <div class="form-group">
            <label class="col-md-3 control-label">Аудитория</label>
            <div class="col-md-8">
                <?= Html::beginTag('select', [name => 'ContestOrganisation[audience_id]', 'class' => 'form-control']) ?>
                <?= Html::renderSelectOptions($model->audience_id, $audiencesArray) ?>  
                <?= Html::endTag('select') ?>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label">Название </label>
            <div class="col-md-8">
                <?= Html::activeTextarea($model, 'name', ['class' => 'form-control']) ?>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label">Расположение </label> 
            <div class="col-md-8">
                <?= Html::beginTag('select', [name => 'ContestOrganisation[location_id]', 'class' => 'form-control']) ?>
                <?= Html::renderSelectOptions($model->location_id, $locationsArray) ?>  
                <?= Html::endTag('select') ?>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label">Дата начала </label>
            <div class="col-md-8">
                <?= Html::activeInput('text', $model, 'start_date', ['class' => 'form-control dateInput']) ?>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label">Дата завершения </label>
            <div class="col-md-8">
                <?= Html::activeInput('text', $model, 'end_date', ['class' => 'form-control dateInput']) ?>
            </div>
        </div>
   
        <div class="form-group">
            <label class="col-md-3 control-label">СОШ </label>
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
            <label class="col-md-3 control-label">География</label>
            <div class="col-md-8">
                <?= Html::activeInput('text', $model, 'geography', ['class' => 'form-control']) ?>
            </div>
        </div>
    
        <?php
            if ($model->contest_organisation_id) {
                echo Html::hiddenInput('contest_organisation_id', $model->contest_organisation_id);
            }
    
        ?>
        
        <?= Html::hiddenInput('action', 'add') ?>
    
        <div class="form-group">
            <div class="col-md-offset-3 col-md-8">
                <input type="submit" class="btn btn-primary" name="submit" value="Сохранить" />
            </div>
        </div>
    
    <?= Html::endForm() ?>
    
</div>

