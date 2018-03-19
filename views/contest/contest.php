<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\helpers\Url;
use app\helpers\DateFormat;

?>

    <?php 
       $url = Url::to(['contest/list']);
       $urlChangeContest = Url::to(['contest/changecontest']);
       
       $teachers = \app\models\Teachers::find()->all();
    ?>

<div ng-controller="contestController" >
    
    <script>
        $(function() {
            $('.dateInput').datepicker({dateFormat: 'dd.mm.yy'});
        });
    </script>
    
    <br/>
    <br/>
    <button type="button" class="showSelectionButton btn btn-default"  >Отбор <span class="glyphicon glyphicon-menu-down"></span></button>
    <div class="selection" style="display: none;">
    <table class="contestTable">
        <tr>
            <td class="col_1"> 
            </td>
            <td class="col_2">
                
                <div class="dropDownContainer" style="display: none;">
                    <div>
                        <input type="text" name="" value="" />
                        <button><span class="glyphicon glyphicon-menu-down" aria-hidden="true"></span> </button>
                    </div>
                    <ul>
                        <?php foreach ($teachers as $t) {

                             ?>
                        <li data-id="<?= $t->teacher_id ?>" ><?= $t->surname ?> <?= $t->name ?> <?= $t->middlename ?></li>
                        <?php } ?>
                    </ul>
                </div>
                
                <select ng-model="selectionData.teacher_id">
                    <option value="">--</option>
                    <option ng-repeat="t in teachers" value="{{t.teacher_id}}" >{{t.surname}} {{t.name}} {{t.middlename}}</option>
                </select>
            </td>
            <td class="col_3">
                <select ng-model="selectionData.audience_id">
                <option value="">--</option>
                        <option ng-repeat="a in audiences" value="{{a.audience_id}}">{{a.name}}</option>
                </select>
            </td>
            <td class="col_4"><input style="width:100px;" type="text" ng-model="selectionData.name"/></td>
            <td class="col_5">
                <select ng-model="selectionData.location_id">
                    <option value="">--</option>
                    <option ng-repeat="l in locations" value="{{l.location_id}}">{{l.name}}</option>
                </select>
            </td>
            <td class="col_6"><input type="text" ng-model="selectionData.start_date" class="dateInput" /></td>
            <td class="col_7"><input type="text" ng-model="selectionData.end_date" class="dateInput" /></td>
            <td class="col_8">
                СОШ <br/>
                <input type="text" ng-model="selectionData.count_soh" /> <br/>
                ССУЗ <br/>
                <input type="text" ng-model="selectionData.count_ssuz" /> <br/>
                ВУЗ <br/> 
                <input type="text" ng-model="selectionData.count_vuz" /> <br/>
            </td>
            <td class="col_9">
                Из Перми <br/>
                <input type="text" ng-model="selectionData.count_member_perm" /> <br/>
                Иногородних <br/>
                <input type="text" ng-model="selectionData.count_member_othercity" /> <br/>
            </td>
            <td class="col_10"><input type="text" ng-model="selectionData.geography" /> </td>
            <td class="col_11"><input type="checkbox" ng-model="selectionData.report_exist" /></td>
        </tr>
    </table>
        <button type="button" class="btn btn-primary" ng-click="applySelection()" >Применить отбор</button>
    </div>
    
    <table class="table table-bordered contestTable" id="contestTable" style="display: none;" >
                        
        <tr>
            <th class="col_1"> 
            </th>
            <th class="col_2">Преподаватель</th>
            <th class="col_3">Целевая аудитория</th>
            <th class="col_4">Название</th>
            <th class="col_5">Место проведения</th>
            <th class="col_6">Дата начала</th>
            <th class="col_7">Дата окончания</th>
            <th class="col_8">Количество учебных заведений, направивших участников</th>
            <th class="col_9">Количество участников</th>
            <th class="col_10">География участников</th>
            <th class="col_11">Наличие на кафедре отчета о мероприятии</th>
        </tr>
        <?php foreach ($contestArray as $contest) { ?>
            <tr>
                <td>
                    <form action="<?= $url ?>" method="POST" >
                        <input type="image" src="img/delete.png" name="submit" value="Удалить" class="contestImageInput" />
                        <input type="hidden" name="action" value="delete" />
                        <input type="hidden" name="contest_id" value="<?= $contest['contest_id']?>" />
                    </form>
                    <form method="POST" onsubmit="showContestChangeForm(<?= $contest['contest_id']?>)" >
                        <input type="image" src="img/change.png" name="submit" value="Изменить" class="contestImageInput" />
                        <input type="hidden" name="contest_id" value="<?= $contest['contest_id']?>" />
                    </form>
                </td>
                <td><?= $contest['teacher_surname']?> <?= $contest['teacher_name']?> <?= $contest['teacher_middlename']?></td>
                <td><?= $contest['audience_name']?></td>
                <td><?= $contest['name']?></td>
                <td><?= $contest['location_name']?></td>
                <td><?= DateFormat::toWebFormat($contest['start_date']) ?></td>
                <td><?= DateFormat::toWebFormat($contest['end_date']) ?></td>
                <td>СОШ <?= $contest['count_soh']?> ССУЗ <?= $contest['count_ssuz']?> ВУЗ <?= $contest['count_vuz']?> </td>
                <td>Из Перми <?= $contest['count_member_perm']?> Иногородних <?= $contest['count_member_othercity']?> </td>
                <td><?= $contest['geography']?></td>
                <td>
                    <input type="checkbox" <?= ($contest['report_exist'] ? 'checked' : '') ?> disabled name="file_exist"/><br/>
                    <?php if ($contest['report_name']) {
                        $fileUrl = Url::to(['contest/get_file', 'contest_id' => $contest['contest_id'], 'report_server_name' => $contest['report_server_name']]);
                    ?>
                    <a href="<?= $fileUrl ?>"><?= $contest['report_name']; ?></a>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
         
    
    <table class="table table-bordered contestTable" id="contestTable" >
        <tr>
            <th class="col_1"> 
            </th>
            <th class="col_2"><a ng-click="sortTable('teacher')">Преподаватель</a></th>
            <th class="col_3"><a ng-click="sortTable('audience')">Целевая аудитория</a></th>
            <th class="col_4"><a ng-click="sortTable('name')">Название</a></th>
            <th class="col_5"><a ng-click="sortTable('location')">Место проведения</a></th>
            <th class="col_6"><a ng-click="sortTable('start_date')">Дата начала</a></th>
            <th class="col_7"><a ng-click="sortTable('end_date')">Дата окончания</a></th>
            <th class="col_8">Количество учебных заведений, направивших участников</th>
            <th class="col_9">Количество участников</th>
            <th class="col_10"><a ng-click="sortTable('geography')">География участников</a></th>
            <th class="col_11"><a ng-click="sortTable('report_exist')">Наличие на кафедре отчета о мероприятии</a></th>
            <th class="col_12"> </th>
        </tr>
            <tr ng-repeat="contest in contestArray" ng-class="(contest.in_rating == '1') ? '' : 'not_in_rating' " >
                <td class="col_1">
                    <form  method="POST" ng-submit="deleteContest(contest['contest_id'])" >
                        <input type="image" src="img/delete.png" name="submit" value="Удалить" class="contestImageInput" />
                        <input type="hidden" name="action" value="delete" />
                        <input type="hidden" name="contest_id" value="{{contest['contest_id']}}" />
                    </form>
                    <form method="POST" ng-submit="showChangeForm(contest['contest_id'], $event)"  >
                        <input type="image" src="img/change.png" name="submit" value="Изменить" class="contestImageInput" />
                        <input type="hidden" name="contest_id" value="{{contest['contest_id']}}" />
                    </form>
                </td>
                <td>{{contest['teacher_surname']}} {{contest['teacher_name']}} {{$contest['teacher_middlename']}} </td>
                <td>{{contest['audience_name']}}</td>
                <td>{{contest['name']}}</td>
                <td>{{contest['location_name']}}</td>
                <td>{{contest['start_date']}}</td>
                <td>{{contest['end_date']}}</td>
                <td>СОШ {{contest['count_soh']}} ССУЗ {{contest['count_ssuz']}} ВУЗ {{contest['count_vuz']}} </td>
                <td>Из Перми {{contest['count_member_perm']}} Иногородних {{contest['count_member_othercity']}} </td>
                <td> {{contest['geography']}} </td>
                <td>
                    <input type='checkbox' disabled name="file_exist" ng-checked="contest['report_exist'] == 1" /> <br/>
                    <a href="{{contest['file_url']}}" ng-if="contest['report_name']">{{contest['report_name']}}</a>
                </td>
                <td> <input type="image" src="{{contest['in_rating'] == '1' ? 'img/Enabled.png' : 'img/Disabled.png'}}" class="inRatingButton" title="{{contest['in_rating'] == '1' ? 'не учитывать в рейтинге' : 'учитывать в рейтинге'}}" ng-click="changeInRating(contest)"  /> </td>
            </tr>
    </table>
    
    <button type="button" class="btn btn-primary" data-toggle="modal" ng-click="showAddForm()" >
        Добавить
    </button>
    
    <br/>
    <br/>
    
    <?php if ($addResult === false) { ?>
        <script> 
            $(document).ready(function() {
                $('#addFormModal').modal('show');
            });
        </script>
    <?php } ?>
        
    <?php if ($changeResult === false) { ?>
        <script> 
            $(document).ready(function() {
                $('#changeFormModal').modal('show');
            });
        </script>
    <?php } ?>
    
    <div class="modal fade" id="addFormModalNew" role="dialog" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Добавление</h4>
                </div>
                <div class="modal-body">
                    <?= $this->render('add_contest_form', ['model' => $addModel, 'type' => 'add', 'addResult' => $addResult, 'addError' => $addError]) ?>
                </div>
            </div>
        </div>
    </div>
     
    <div class="modal fade" id="changeFormModalNew" role="dialog" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Изменение</h4>
                </div>
                <div class="modal-body">
                    <?php 

                        echo $this->render('change_contest_form', ['model' => $changeModel, 'type' => 'change', 'changeResult' => $changeResult, 'changeError' => $changeError]);
                    
                    ?>
                </div>
            </div>
        </div>
    </div>    
        
    <?php if (false) { ?>
    <div class="modal fade" id="changeFormModal" role="dialog" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Изменение</h4>
                </div>
                <div class="modal-body">
                    <?php 
                    if ($changeResult === false) {
                        echo $this->render('change_contest_form', ['model' => $changeModel, 'type' => 'change', 'changeResult' => $changeResult, 'changeError' => $changeError]);
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    
    <br/>
    <br/>
    
    <form action="" onsubmit="return submitTestFileForm(this)" style="display: none;" >
        <input type="text" name="myText" />
        <input type="file" name="myFile"  />
        <input type="submit" name="submit" value="Отправить" />
    </form>
    
    <br/>
    <br/>
    
</div>

