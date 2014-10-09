<?php
/* @var $this ClassPlanController */
/* @var $model ClassPlan */

$this->breadcrumbs=array(
	'Projetos'=>array('index'),
	'Criar Projeto',
);

$this->menu=array(
	array('label'=>'Listar Projetos', 'url'=>array('index')),
	array('label'=>'Gerenciar Projetos', 'url'=>array('admin')),
);
?>

<h3>Criar Projeto</h3>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>