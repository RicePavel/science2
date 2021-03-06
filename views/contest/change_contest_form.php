

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
    
    $locations = Locations::find()->all();
    
    $locationsArray = [];
    foreach ($locations as $location) {
        $locationsArray[$location->location_id] = $location->name;
    }
    
    ?>
    
    <form class="form-horizontal" enctype='multipart/form-data' id="changeContestForm" ng-submit='submitChangeForm($event)' >
        
        <div class="form-group">
            <label class="col-md-3 control-label">Преподаватель</label>
            <div class="col-md-8">
                <select class='form-control' ng-model="contestForChange.teacher_id">
                    <option ng-repeat="t in teachers" value="{{t.teacher_id}}">{{t.surname}} {{t.name}} {{t.middlename}}</option>
                </select>
            </div>
        </div>
    
        <div class="form-group">
            <label class="col-md-3 control-label">Целевая аудитория</label>
            <div class="col-md-8">
                <select class="form-control" ng-model="contestForChange.audience_id">
                    <option ng-repeat="a in audiences" value="{{a.audience_id}}">{{a.name}}</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label">Название</label>
            <div class="col-md-8">
                <textarea name='name' class='form-control' ng-model = 'contestForChange.name'> </textarea>
            </div>
        </div>
        
        <div class="form-group locationFormGroup">
            <label class="col-md-3 control-label">Место проведения</label> 
            <div class="col-md-8">
                <div class="input-group">
                    <input type="text" name="" class="form-control locationTextInput" value="{{contestForChangeExtra.locationName}}" required />
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
                <input type="text" name="Contest[start_date]"  class="form-control dateInput" ng-model='contestForChange.start_date' />
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label">Дата окончания</label>
            <div class="col-md-8">
                <input type="text" name="Contest[end_date]"  class="form-control dateInput" ng-model='contestForChange.end_date'/>
            </div>
        </div>
   
        <div class="form-group">
            <label class="col-md-3 control-label">СОШ</label>
            <div class="col-md-8">
                <input type="text" name="Contest[count_soh]"  class="form-control" ng-model='contestForChange.count_soh'/>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label">ССУЗ</label>
            <div class="col-md-8">
                <input type="text" name="Contest[count_ssuz]"  class="form-control" ng-model='contestForChange.count_ssuz'/>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label">ВУЗ</label>
            <div class="col-md-8">
                <input type="text" name="Contest[count_vuz]"  class="form-control" ng-model='contestForChange.count_vuz'/>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label">Из Перми</label>
            <div class="col-md-8">
                <input type="text" name="Contest[count_member_perm]"  class="form-control" ng-model='contestForChange.count_member_perm'/>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label">Иногородних</label>
            <div class="col-md-8">
                <input type="text" name="Contest[count_member_othercity]"  class="form-control" ng-model='contestForChange.count_member_othercity'/>
            </div>
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label">География участников</label>
            <div class="col-md-8">
                <input type="text" name="Contest[geography]"  class="form-control" ng-model='contestForChange.geography'/>
            </div>
        </div>
    
        <div class="form-group">
            <!-- сюда записывается инфа для js о том, есть ли файл на сервере -->
            <input type="hidden" id="fileOnServerExist" value="{{contestForChange.report_server_name !== null ? '1' : ''}}" />
            
            <input type="hidden" name="report_deleted" value="" />
            
            <label class="col-md-3 control-label" >Файл</label>
            <div class="col-md-8"> 
                <input type="checkbox" class="contestFileCheckbox" value="1" name="Contest[report_exist]" ng-checked='contestForChange.report_exist' ng-model='contestForChange.report_exist' /> <br/>
                
                <input type="file" ng-show='contestForChange.report_exist === true && contestForChange.report_name === null'  class="contestFileInput" name="report" /> 
                
                <div class="contestFileLinkContainer" ng-if='contestForChange.report_name !== null'>
                    <a class="contestFileLink" href="{{contestForChangeExtra.fileUrl}}">{{contestForChange.report_name}}</a> &nbsp; &nbsp;
                    <a href="#" ng-click='deleteReportInChangeModel($event)' > <img src="img/delete.png" style="width:20px;"/> </a>
                </div>
    
            </div>
        </div>
     
        <input type="hidden" name="contest_id" value="{{contestForChange.contest_id}}"/>
    
        <div class="form-group">
            <div class="col-md-offset-3 col-md-8">
                <input type="submit" class="btn btn-primary" name="submit" value="Сохранить" />
            </div>
        </div>
    
    </form>
    
</div>

