<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AntiplagiatReports */

$this->title = 'Отчет #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Отчеты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
echo"<div class='antiplagiat-reports-view'>

    <h1>";
echo Html::encode($this->title); 
echo '</h1>
    <div class="help-block">Время создания: ';
echo($model->created_at); 
echo"</div>
    <p>
        ";
echo Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Отчет будет удален, сохраните его ID',
                'method' => 'post',
            ],
        ]); 
echo"</p>
    <h3>Статья ";
echo(Html::a('#' . $model->file->submission_id, $model->articleUrl(), ['target' => '__blank']));
echo"</h3>
    <p>
       ";
echo Html::a($model->articleTitle(), $model->articleUrl(), ['target' => '__blank']);
echo"
    </p>
    <h3>Исключения</h3>";
function prettyPrint( $json )
    {
        $result = '';
        $level = 0;
        $in_quotes = false;
        $in_escape = false;
        $ends_line_level = NULL;
        $json_length = strlen( $json );

        for( $i = 0; $i < $json_length; $i++ ) {
            $char = $json[$i];
            $new_line_level = NULL;
            $post = "";
            if( $ends_line_level !== NULL ) {
                $new_line_level = $ends_line_level;
                $ends_line_level = NULL;
            }
            if ( $in_escape ) {
                $in_escape = false;
            } else if( $char === '"' ) {
                $in_quotes = !$in_quotes;
            } else if( ! $in_quotes ) {
                switch( $char ) {
                    case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                    case '{': case '[':
                    $level++;
                    case ',':
                        $ends_line_level = $level;
                        break;

                    case ':':
                        $post = " ";
                        break;

                    case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
                }
            } else if ( $char === '\\' ) {
                $in_escape = true;
            }
            if( $new_line_level !== NULL ) {
                $result .= "\n".str_repeat( "\t", $new_line_level );
            }
            $result .= $char.$post;
        }

        return $result;
    }

 if ($model->exceptions) {
	echo'
        <table class="table">
            <thead>
            <tr>
                <th>
                    Тип исключения
                </th>
                <th>
                    Сообщение
                </th>
                <th>
                    Время
                </th>
            </tr>
            </thead>';
        foreach ($model->exceptions as $exception) {
            echo"<tr>
                <td>";
            echo $exception->exception; 
            echo '</td>
                <td>';
            echo $exception->message;
            echo'</td>
                <td>';
            echo $exception->created_at;
            echo '   </td>
            </tr>';
        }
        echo "</table>";
 }        
 else { 
	echo'
    <div class="help-block">
        Исключение не было
    </div>
    <?php } ?>

    <h3>Лог запросов</h3>';
    if ($model->logs) {
	echo'
        <table class="table">
            <thead>
            <tr>
                <th>
                    Время
                </th>
                <th>
                    Метод
                </th>
                <th>
                    Ответ
                </th>
            </tr>
            </thead>  ';
        foreach ($model->logs as $log) {
                echo'<tr>
                    <td>
                        <?= $log->created_at ?>
                    </td>
                    <td>
                        <?= $log->method ?>
                    </td>
                    <td>
                        <pre>';
		echo(json_decode($log->data) ? prettyPrint(trim($log->data)) : $log->data);
		echo'	</pre>
                    </td>

                </tr>';
       }
       echo' </table>';
    }
    else {
        echo '<div class="help-block">
            Запросов не было
        </div>';
   }
echo '</div>';
}