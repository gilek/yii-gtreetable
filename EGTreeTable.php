<?php

/**
* @link https://github.com/gilek/yii-gtreetable
* @copyright Copyright (c) 2015 Maciej Kłak
* @license https://github.com/gilek/yii-gtreetable/blob/master/LICENSE
*/

class EGTreeTable extends CWidget
{

    public $options = array();
    public $htmlOptions = array();
    public $selector;
    public $columnName;
    public $baseScriptUrl;
    public $minSuffix;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->registerClientScript();
        if ($this->minSuffix === null) {
            $this->minSuffix = YII_DEBUG ? '' : '.min';
        }
        if ($this->columnName === null) {
            $this->columnName = Yii::t('gtreetable', 'Name');
        }
    }

    public function run()
    {
        if ($this->selector === null) {
            $htmlOptions = array_merge(array(
                'class' => 'table gtreetable',
                'id' => $this->getId()
                    ), $this->htmlOptions);

            echo CHtml::openTag('table', $htmlOptions);
            echo CHtml::openTag('thead');
            echo CHtml::openTag('tr');
            echo CHtml::openTag('th');
            echo $this->columnName;
            echo CHtml::closeTag('th');
            echo CHtml::closeTag('tr');
            echo CHtml::closeTag('thead');
            echo CHtml::closeTag('table');
        }
    }

    public function registerClientScript()
    {
        $assetsDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
        if ($this->baseScriptUrl === null) {
            $this->baseScriptUrl = Yii::app()->getAssetManager()->publish($assetsDir);
        }

        $cs = Yii::app()->getClientScript();
        $cs->registerCoreScript('jquery');

        $cs->registerScriptFile($this->baseScriptUrl . '/bootstrap-gtreetable/dist/bootstrap-gtreetable' . $this->minSuffix . '.js');
        $cs->registerCssFile($this->baseScriptUrl . '/bootstrap-gtreetable/dist/bootstrap-gtreetable' . $this->minSuffix . '.css');

        if (array_key_exists('language', $this->options) && strlen($this->options['language']) > 0 && $this->options['language'] !== 'en-US') {
            $langFile =  '/bootstrap-gtreetable/dist/languages/bootstrap-gtreetable.' . $this->options['language'] . $this->minSuffix . '.js';            
            if (file_exists($assetsDir . DIRECTORY_SEPARATOR .$langFile)) {
                $cs->registerScriptFile($this->baseScriptUrl . $langFile);
            }
        }

        if (array_key_exists('draggable', $this->options) && $this->options['draggable'] === true) {
            $cs->registerCoreScript('jquery.ui');
            $cs->registerScriptFile($this->baseScriptUrl . '/jquery.browser/dist/jquery.browser' . $this->minSuffix . '.js');
        }

        $selector = $this->selector === null ? '#' . (array_key_exists('id', $this->htmlOptions) ? $this->htmlOptions['id'] : $this->getId()) : $this->selector;

        $script = 'jQuery(\'' . $selector . '\').gtreetable(' . CJavaScript::encode($this->options) . ')';
        $cs->registerScript(uniqid(), $script . ';', CClientScript::POS_READY);
    }

}
