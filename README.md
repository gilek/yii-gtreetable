# Yii-GTreeTable

Yii-GTreeTable is extension of Yii framework which is wrapper for [bootstrap-gtreetable](https://github.com/gilek/bootstrap-gtreetable) plugin, on the other hand provides support to server side application.

Thanks to software it's possible to map actual state of nodes to data base.

Test available on [demo project](http://gtreetable.gilek.net).

![](http://gilek.net/images/gtt2-demo.png)

## Requirements
- PHP 5.2 or above,
- Yii 1.1 or above,
- Twitter Bootstrap 3.

For Yii2 see [yii2-gtreetable](https://github.com/gilek/yii2-gtreetable).

## Minimal configuration<a name="minimal-configuration"></a>

1. Create table to store nodes:

  ``` sql
  CREATE TABLE `tree` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `root` INT(10) UNSIGNED DEFAULT NULL,
    `lft` INT(10) UNSIGNED NOT NULL,
    `rgt` INT(10) UNSIGNED NOT NULL,
    `level` SMALLINT(5) UNSIGNED NOT NULL,
    `type` VARCHAR(64) NOT NULL,
    `name` VARCHAR(128) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `root` (`root`),
    KEY `lft` (`lft`),
    KEY `rgt` (`rgt`),
    KEY `level` (`level`)
  );
  ```

2. Add main node:

  ``` sql
  INSERT INTO `tree` (`id`, `root`, `lft`, `rgt`, `level`, `type`, `name`) VALUES (1, 1, 1, 2, 1, 'default', 'Main node');
  ```

3. Copy project to the `protected/extensions` folder.

4. Create `Tree` model, based on table described in point 1. It's important that model extend `models\BaseModel` class:

  ``` php
  <?php
  Yii::import('ext.gtreetable.models.BaseModel');  
  
  class Tree extends BaseModel 
  {
    
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
 
    public function tableName() {
        return 'tree';
    }
  }
  ```
  
4. Create new controller or add to existing one following actions:	
  ``` php
  <?php
  class TreeController extends CController {    

    public $modelName = 'Tree';
    
    public function actions() {
      return array(
        'nodeChildren'=>array(
          'class'=>'ext.gtreetable.actions.NodeChildrenAction',
          'treeModelName'=>$this->modelName,
        ),
        'nodeCreate'=>array(
          'class'=>'ext.gtreetable.actions.NodeCreateAction',
          'treeModelName'=>$this->modelName,
        ),      
        'nodeUpdate'=>array(
          'class'=>'ext.gtreetable.actions.NodeUpdateAction',
          'treeModelName'=>$this->modelName,
        ),  
        'nodeDelete'=>array(
          'class'=>'ext.gtreetable.actions.NodeDeleteAction',
          'treeModelName'=>$this->modelName,
        ),             
        'nodeMove'=>array(
          'class'=>'ext.gtreetable.actions.NodeMoveAction',
          'treeModelName'=>$this->modelName,
        ),              
      );
  	}      
    
    public function actionIndex() {
    	$this->render('ext.gtreetable.views.widget');
    }
	} 
	```

## Configuration

### Actions

All actions from `actions` location have `treeModelName` property, which is reference to model data extending form `models\BaseModel` (see [Minimal configuration](#minimal-configuration) point 4).

### Model 
 
Support of tree structure in data base is based on [Nested set model](http://en.wikipedia.org/wiki/Nested_set_model).

Abstract class `models\BaseModel` provides Nested set model on PHP side. It defines validation rules and other required methods. Its configuration can by adjusted by parameters:

  + `$hasManyRoots` (boolean) - define whether is possible to create more than one main node. Default `true`,

  + `$leftAttribute` (string) - column name storing left value. Default `lft`,  

  + `$levelAttribute` (string) - column name storing level of node. Defualt `level`,  

  + `$nameAttribute` (string) - column name storing label of node. Defualt `name`,    

  + `$rightAttribute` (string) - column name storing left value. Default `rgt`,   

  + `$rootAttribute` (string) - column name storing reference to main element ID. Default `root`,  

  + `$typeAttribute` (string) - column name storing type of node. Default `type`.  

### View

`views\widget` view class consists configuration of [CUD operation](https://github.com/gilek/bootstrap-gtreetable#cud) with reference to [nodes source](https://github.com/gilek/bootstrap-gtreetable#source). There is no necessity to use it, but it can be very helpful in simple projects. 

Class may be adjusted by properties:

  + `$controller` (string) - controller name where the actions are defined (see [Minimal configuration](#minimal-configuration) point 4). By default is getting the controller name where the `views\widget` view was triggered,

  + `$options` (array) - options supplied directly to bootstrap-gtreetable plugin,

  + `$routes` (array) - in the case when particular nodes are located in different containers or its name is different in relation to presented in point 4 of the chapter [Minimal configutarion](#minimal-configutarion), then it's necessary to define it,

  Following example shows structure of data:

  ``` php
  <?php
  [
    'nodeChildren' => 'controllerA/source',
    'nodeCreate' => 'controllerB/create',
    'nodeUpdate' => 'controllerC/update',
    'nodeDelete' => 'controllerD/delete',
    'nodeMove' => 'controllerE/move'
  ]
  ?>
  ```

### Widget   

The main task of `EGTreeTable` widget is generate parameters to bootstrap-gtreetable plugin and adding required files.
When container in not available he also response for creating it. Class has following properties:

  + `$baseScriptUrl` (string) - the base script URL for resources, 

  + `$columnName` (string) - table column name. Default value is `Name` which is getting from translation file,

  + `$htmlOptions` (array) - html options of container, they are rendering in the moment of its creation (parameter `$selector` set on `null`),  

  + `$options` (array) - options supplied directly to bootstrap-gtreetable plugin,  

  + `$selector` (string) - jQuery selector indicated on tree container (`<table>` tag). When parameter is set on `null`, table will be automatically created. Default `null`.

## Limitations

Yii-GTreeTable use [Nested Set behavior](https://github.com/yiiext/nested-set-behavior) extension, which in for the moment (October 2014) has some limitation regarding ordering main elements (nodes which level = 1). 

In case of adding or moving node as the main node, then it will be located after last element in this level. Therefore order of displayed main nodes may not have the same mapping in data base.
