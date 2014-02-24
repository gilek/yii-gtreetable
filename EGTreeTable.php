<?php
/*
* @author Maciej "Gilek" Kłak
* @copyright Copyright &copy; 2014 Maciej "Gilek" Kłak
* @version 1.1a
* @package Yii-GTreeTable
*/
class EGTreeTable extends CWidget {        
    public $options = array();
    public $htmlOptions = array();
    public $selector;   
    public $baseScriptUrl;  
    public $columnName = 'Category';
    
    public function run() {
        if($this->baseScriptUrl===null) {
            $this->baseScriptUrl=Yii::app()->getAssetManager()->publish(dirname(__FILE__).DIRECTORY_SEPARATOR.'assets');
        }

        $cs=Yii::app()->getClientScript();  
        $cs->registerCoreScript('jquery');
        
        $cs->registerScriptFile($this->baseScriptUrl.'/bootstrap-gtreetable.js');        
        $cs->registerCssFile($this->baseScriptUrl.'/gtreetable.css');
        
        if (array_key_exists('language', $this->options) && strlen($this->options['language']) > 0) {
            $cs->registerScriptFile($this->baseScriptUrl.'/languages/bootstrap-gtreetable.'.$this->options['language'].'.js');              
        }
        
        $selector = $this->selector;
        if ($selector===null) {       
            $htmlOptions = array_merge(
                array(
                    'class'=>'table gtreetable',
                    'id'=>$this->getId()
                ),
                $this->htmlOptions
            );
            
            $selector = '#'.$htmlOptions['id'];
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

        $script = 'jQuery(\''.$selector.'\').gtreetable('.CJavaScript::encode($this->options).')';
        $cs->registerScript(uniqid(),$script.';', CClientScript::POS_READY);
    }
}
