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
    
    <table class="table table-bordered" id="contestTable" >
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
                    <form method="POST" ng-submit="showChangeForm(<?= $contest['contest_id'] ?>)" >
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
                <td><?= $contest['report_exist']?></td>
            </tr>
        <?php } ?>
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
                    <?= $this->render('contestform', ['model' => $addModel, 'type' => 'add', 'addResult' => $addResult, 'addError' => $addError]) ?>
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
                    <?= $this->render('contestform', ['model' => $changeModel, 'type' => 'change', 'changeResult' => $changeResult, 'changeError' => $changeError]) ?>
                </div>
            </div>
        </div>
    </div>
    
    
    <br/>
    <br/>
    
</div>

