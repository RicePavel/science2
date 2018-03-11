<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\helpers\Url;

?>

    <?php 
       $url = Url::to(['contest/list']);
       $urlChangeContest = Url::to(['contest/changecontest']);
    ?>

<div>
    
    <script>
        $(function() {
            $('.dateInput').datepicker({dateFormat: 'dd.mm.yy'});
        });
    </script>
    
    <table class="table table-bordered">
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
                        <input type="submit" name="submit" value="Удалить" />
                        <input type="hidden" name="action" value="delete" />
                        <input type="hidden" name="contest_organisation_id" value="<?= $contest['contest_organisation_id']?>" />
                    </form>
                    <form action="<?= $urlChangeContest ?>" method="POST" >
                        <input type="submit" name="submit" value="Изменить" />
                        <input type="hidden" name="contest_organisation_id" value="<?= $contest['contest_organisation_id']?>" />
                    </form>
                </td>
                <td><?= $contest['teacher_surname']?> <?= $contest['teacher_name']?> <?= $contest['teacher_middlename']?></td>
                <td><?= $contest['audience_name']?></td>
                <td><?= $contest['name']?></td>
                <td><?= $contest['location_name']?></td>
                <td><?= $contest['start_date']?></td>
                <td><?= $contest['end_date']?></td>
                <td>СОШ <?= $contest['count_soh']?> ССУЗ <?= $contest['count_ssuz']?> ВУЗ <?= $contest['count_vuz']?> </td>
                <td>Из Перми <?= $contest['count_member_perm']?> Иногородних <?= $contest['count_member_othercity']?> </td>
                <td><?= $contest['geography']?></td>
                <td><?= $contest['report_exist']?></td>
            </tr>
        <?php } ?>
    </table>
     
    
    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
  Launch demo modal
</button>

<!-- Модаль -->  

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
    
    
    
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addFormModal" >
        Добавить
    </button>
    
    <br/>
    <br/>
    
    <div class="modal fade" id="addFormModal" role="dialog" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Добавление</h4>
                </div>
                <div class="modal-body">
                    <?= $this->render('contestform', ['model' => $model]) ?>
                </div>
            </div>
        </div>
    </div>
    
    <br/>
    <br/>
    
</div>

