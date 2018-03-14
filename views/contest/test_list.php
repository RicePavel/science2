<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\helpers\Url;
use app\helpers\DateFormat;

?>

<table>
<?php  foreach ($contestArray as $contest) { ?>
    <tr>
        <td><?= $contest['teacher_surname'] ?> <?= $contest['teacher_name'] ?></td>
        <td><?= $contest->name ?> </td>
    </tr>
<?php  } ?>
</table>