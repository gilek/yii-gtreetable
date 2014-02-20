Yii-GTreeTable
==============
Yii-GTreeTable is an extension of [Yii Framework](http://yiiframework.com), which is wrapper for [GTreeTable plug-in](http://github.com/gilek/gtreetable),
on the other hand it provides functionality which allows to save the nodes states into database.

See [live demo](http://gtreetable.gilek.net).

![](http://gtreetable.gilek.net/assets/gtreetable-demo.png)

Requirements
------------
- PHP 5.2 or above,
- Yii 1.1 or above,
- Twitter Bootstrap 3.

Installing and configuring
--------------------------
1.&nbsp;Create table to store the nodes states:

```sql
CREATE TABLE `tree` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `type` varchar(128) NOT NULL,
  `name` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `lft` (`lft`),
  KEY `rgt` (`rgt`),
  KEY `level` (`level`),
  KEY `name` (`name`)
);
```

2.&nbsp;Add the main node:

```sql
INSERT INTO `tree` (`id`, `lft`, `rgt`, `level`, `type`, `name`) VALUES (1, 0, 1, 0, 'root', 'Root');
```

3.&nbsp;Copy project to the `protected/extensions` folder.

4.&nbsp;Create the `Unit` model, extending `ActivRecord` based on `tree` table.

5.&nbsp;In `Unit` model define:

a).Constants and parent property:
```php
const TYPE_ROOT = 'root';
const TYPE_DEFAULT = 'default';

public $parent;
```

b). String representation of object:
```php
public function __toString() {
	return $this->name;
}
```

c). Validation rules for model attributes:
```php
public function rules() {
	return array(
		array('parent', 'required', 'on' => 'create'),
		array('name', 'required'),
		array('name', 'length', 'max' => 128),
	);
}
```

d). Action before saving operation:
```php
public function beforeSave() {
	parent::beforeSave();
	if ($this->isNewRecord)
		$this->type = self::TYPE_DEFAULT;
	return true;
}
```

e). Label for name attribute:
```php
public function attributeLabels() {
	return array(
		'name' => 'Category name',
	);
}
```

f). Behaviours:
```php
public function behaviors() {
	return array(
		'nestedSetBehavior'=>array(
			'class'=>'ext.gtreetable.behaviors.nestedset.NestedSetBehavior',
		),            
	);
}
```

g). Relations:
```php
public function relations() {
	return array(
		'parentRel' => array(self::BELONGS_TO, 'Unit', 'parent'),
	);
}
```

6.&nbsp;Create `TreeController` controller and define following actions:

```php
public function actions() {
	return array(
		'nodeChildren'=>array(
			'class'=>'ext.gtreetable.actions.NodeChildrenAction',
			'treeModelName'=>'Unit',
		),
		'nodeCreate'=>array(
			'class'=>'ext.gtreetable.actions.NodeCreateAction',
			'treeModelName'=>'Unit',
		),      
		'nodeUpdate'=>array(
			'class'=>'ext.gtreetable.actions.NodeUpdateAction',
			'treeModelName'=>'Unit',
		),  
		'nodeDelete'=>array(
			'class'=>'ext.gtreetable.actions.NodeDeleteAction',
			'treeModelName'=>'Unit',
		),             
	);
}   	

public function actionIndex() {
	$this->pageTitle = 'GTreetable';
	$this->render('tree');
}
```
	
7.&nbsp;In `proteced/views/tree` folder, create "view" file `tree.php` and define its content:

```php
<?php $this->widget('ext.gtreetable.EGTreeTable',array(
    'htmlOptions'=>array('id'=>'gtreetree'),
    'options'=>array(
        'source' => "js:function(id) { 
            return '".$this->createUrl('tree/nodeChildren')."'+'?id='+id; 
        }",
        'onSave' => "js:function(node) {
            return jQuery.ajax({
                type: 'POST',
                url: !node.hasClass('node-saved') ? '".$this->createUrl('tree/nodeCreate')."' : '".$this->createUrl('tree/nodeUpdate')."'+'?id='+node.data('id'),
                data: {
                    'parent': node.data('parent'),
                    'name': node.find('.node-action input').val()
                },
                dataType: 'json',
                error: function(XMLHttpRequest) {
                    alert(XMLHttpRequest.status+': '+XMLHttpRequest.responseText);
                }
            });        
        }",
        'onDelete' => "js:function(node) {
            return jQuery.ajax({
                type: 'POST',
                url: '".$this->createUrl('tree/nodeDelete')."'+'?id='+node.data('id'),
                data: {
                    'id' : node.data('id')
                },
                dataType: 'json',
                error: function(XMLHttpRequest) {
                    alert(XMLHttpRequest.status+': '+XMLHttpRequest.responseText);
                }
            });        
        }"        
    )
)) ?>
```
	
