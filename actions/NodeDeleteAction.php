<?php

/*
 * @author Maciej "Gilek" Kłak
 * @copyright Copyright &copy; 2014 Maciej "Gilek" Kłak
 * @version 2.0.0-alpha
 * @package yii-gtreetable
 */

Yii::import('ext.gtreetable.actions.BaseAction');

class NodeDeleteAction extends BaseAction
{
    
    public function run($id) {
        $model = $this->getNodeById($id);

        if ($model->isRoot() && (integer) $model->roots()->count() === 1) {
            throw new CHttpException(500, Yii::t('gtreetable', 'Main element can`t be deleted!'));
        }

        $nodes = $model->descendants()->findAll();
        $nodes[] = $model;

        $trans = $model->dbConnection->beginTransaction();
        try {
            if (!$model->deleteNode()) {
                throw new CDbException(Yii::t('gtreetable', 'Deleting operation `{name}` failed!', array('{name}' => Html::encode((string) $model))));
            }

            $trans->commit();
            return true;
        } catch (CException $e) {
            $trans->rollBack();
            throw new CHttpException(500, $e->getMessage());
        }
    }    
}
