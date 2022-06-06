<?php

namespace app\controllers;

use app\models\Journals;
use Yii;

class PackageController extends \yii\web\Controller
{
    public function actionJournalsettings()
    {


        if (Yii::$app->request->post('from') && Yii::$app->request->post('to')){
            if(Yii::$app->request->post('from')!=Yii::$app->request->post('to')){
                $deniedCopySettings=[
                    'initials',
                    'title',
                ];
                $sql='DELETE FROM journal_settings WHERE journal_id='.Yii::$app->request->post('to').' AND setting_name NOT IN ("'.implode('","',$deniedCopySettings).'")';
                Yii::$app->db->createCommand($sql)->execute();
                $sql='INSERT INTO journal_settings (journal_id,locale,setting_name,setting_value,setting_type)  SELECT "'.Yii::$app->request->post('to').'",locale,setting_name,setting_value,setting_type FROM journal_settings WHERE journal_id='.Yii::$app->request->post('from').' AND setting_name NOT IN ("'.implode('","',$deniedCopySettings).'") ';
                Yii::$app->db->createCommand($sql)->execute();


                $sql='DELETE FROM plugin_settings WHERE journal_id='.Yii::$app->request->post('to').' ';
                Yii::$app->db->createCommand($sql)->execute();
                $sql='INSERT INTO plugin_settings (plugin_name,locale,journal_id,setting_name,setting_value,setting_type)  SELECT plugin_name,locale,'.Yii::$app->request->post('to').',setting_name,setting_value,setting_type FROM plugin_settings WHERE journal_id='.Yii::$app->request->post('from').'  ';
                Yii::$app->db->createCommand($sql)->execute();

                Yii::$app->getSession()->setFlash('success', 'Настройки были скопированы');
            } else {
                Yii::$app->getSession()->setFlash('error', 'Выбранные журналы должны быть разными');
            }

        }

        $journals=Journals::find()->with('settings')->all();
        foreach ($journals as $journal){
            $journal->data=new \stdClass();
            foreach($journal->settings as $settingRow){
                $journal->data->{$settingRow->setting_name}=$settingRow->setting_value;
            }
        }
        return $this->render('journalsettings',array('journals'=>$journals));
    }

}
