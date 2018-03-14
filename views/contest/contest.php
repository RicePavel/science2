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
    ?>

<div ng-controller="contestController" >
    
    <script>
        $(function() {
            $('.dateInput').datepicker({dateFormat: 'dd.mm.yy'});
        });
    </script>
    
    <table class="table table-bordered" id="contestTable" style="display: none;" >
        <tr>
            <th> 
            </th>
            <th>Преподаватель</th>
            <th>Целевая аудитория</th>
            <th>Название</th>
            <th>Место проведения</th>
            <th>Дата начала</th>
            <th>Дата окончания</th>
            <th>Количество учебных заведений, направивших участников</th>
            <th>Количество участников</th>
            <th>География участников</th>
            <th>Наличие на кафедре отчета о мероприятии</th>
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
         
    
    <table class="table table-bordered" id="contestTable" >
        <tr>
            <th> 
            </th>
            <th><a ng-click="sortTable('teacher')">Преподаватель</a></th>
            <th><a ng-click="sortTable('audience')">Целевая аудитория</a></th>
            <th><a ng-click="sortTable('name')">Название</a></th>
            <th><a ng-click="sortTable('location')">Место проведения</a></th>
            <th><a ng-click="sortTable('start_date')">Дата начала</a></th>
            <th><a ng-click="sortTable('end_date')">Дата окончания</a></th>
            <th>Количество учебных заведений, направивших участников</th>
            <th>Количество участников</th>
            <th><a ng-click="sortTable('geography')">География участников</a></th>
            <th><a ng-click="sortTable('report_exist')">Наличие на кафедре отчета о мероприятии</a></th>
        </tr>
            <tr ng-repeat="contest in contestArray">
                <td>
                    <form  method="POST" ng-submit="deleteContest(contest['contest_id'])" >
                        <input type="image" src="img/delete.png" name="submit" value="Удалить" class="contestImageInput" />
                        <input type="hidden" name="action" value="delete" />
                        <input type="hidden" name="contest_id" value="{{contest['contest_id']}}" />
                    </form>
                    <form method="POST" ng-submit="submitChangeForm(contest['contest_id'])"  >
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
            </tr>
    </table>
    
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addFormModal" >
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
    
    <div class="modal fade" id="addFormModal" role="dialog" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Добавление</h4>
                </div>
                <div class="modal-body">
                    <?= $this->render('add_contest_form', ['model' => $addModel, 'type' => 'add', 'addResult' => $addResult, 'addError' => $addError]) ?>
                </div>
            </div>
        </div>
    </div>
    
    
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
    
    
    <br/>
    <br/>
    
</div>

