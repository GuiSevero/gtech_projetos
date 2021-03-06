<?php
/* @var $this ClassPlanController */
/* @var $model ClassPlan */
/* @var $form CActiveForm */
$baseUrl = Yii::app()->request->baseUrl;
Yii::app()->clientScript->registerScriptFile($baseUrl .'/js/jquery.tokeninput.js');
Yii::app()->clientScript->registerScriptFile('https://www.google.com/jsapi');
Yii::app()->clientScript->registerCSSFile($baseUrl .'/css/tokeninput/token-input.css');
Yii::app()->clientScript->registerCSSFile($baseUrl .'/css/tokeninput/token-input-facebook.css');
Yii::app()->clientScript->registerScriptFile($baseUrl .'/js/tinymce/tinymce.min.js');
Yii::app()->clientScript->registerScriptFile($baseUrl .'/js/sobek.js');

$url_tokens = Yii::app()->createUrl('/site/tokens');
$url_new_token = Yii::app()->createUrl('/site/addToken');

Yii::app()->clientScript->registerScript('helper_script',"
	
	var population = $('#ClassPlan_tags').val().split(' ');

	if($('#ClassPlan_tags').val() == '')
		population = null;

	var prePop = Array();
	for( i in population){
		prePop.push({
			id: null,
			name: population[i]
		});
	}

	$('#ClassPlan_tags').tokenInput('{$url_tokens}',{
			searchingText: 'Buscando'
		,	hintText: 'Digite um nome'
		,	noResultsText: 'Resultado não encontrado - Pressione \'Enter\' ou \'Tab\' para adiconá-lo'
		,	theme:'facebook'
		,	preventDuplicates: true
		,	allowFreeTagging: true
		,	prePopulate: (prePop.length > 0) ? prePop : null
		,	tokenValue: 'name'
		,	tokenDelimiter: ' '
		,	onFreeTaggingAdd: function(item){
				$.post('{$url_new_token}',{token:item});
				return item;
			},

	});

	$('#ClassPlan_theme').change(function(){
		var src = \"{$baseUrl}/css/templates/\" + $(this).val() + '.png';
		$('#theme-thumbnail').attr('src', src);

	});


");


?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'class-plan-form',
	'enableAjaxValidation'=>false,
	'errorMessageCssClass'=>'text-danger',
	'htmlOptions'=>array('class'=>'form-horizontal', 'role'=>'form'),
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<?php
		 $header = "<div class=\"alert alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>";
		 $footer = "</div>";
		//echo $form->errorSummary($model, $header, $footer); 
	?>
<div class="row">
<div class="col-sm-12">

	<div class="form-group">
		<?php echo $form->labelEx($model,'title', array('class'=>'col-sm-2 control-label')); ?>
		<div class="col-sm-10">
			<?php echo $form->textField($model,'title', array('class'=>'form-control')); ?>
			<?php echo $form->error($model,'title'); ?>
		</div>
	</div>

	<div class="form-group">
		<?php echo $form->labelEx($model,'description', array('class'=>'col-sm-2 control-label')); ?>
		<div class="col-sm-10">
			<?php echo $form->textArea($model,'description', array('class'=>'form-control input-xlg','rows'=>5, 'cols'=>50)); ?>
			<?php echo $form->error($model,'description'); ?>
		</div>
	</div>


	<div class="form-group">
		<?php echo $form->labelEx($model,'released', array('class'=>'col-sm-2 control-label')	); ?>
		<div class="col-sm-10">
			<?php if($model->released): ?>
				<p class="form-control">
					<?php $text = "http://{$_SERVER['SERVER_NAME']}{$this->createUrl('/classPlan/plan', array('id'=>$model->id_class))}"; ?>
					<?php echo CHtml::link($text, array('/classPlan/plan', 'id'=>$model->id_class), array('target'=>'blank')); ?>
				</p>	
			<?php else: ?>
				<p class="form-control">Projeto ainda não publicado</p>	
		   <?php endif; ?>
			<?php echo $form->hiddenField($model,'released'); ?>
			<?php echo $form->error($model,'released'); ?>
		</div>
	</div>


	<!-- Tab panes -->
	<div class="row">
		<div class="col-sm-8">
		<div class="form-group">
			<?php echo $form->labelEx($model,'contents', array('class'=>'col-sm-2 control-label')); ?>
			<div class="col-sm-10">
				<?php echo $form->textArea($model,'contents',array('rows'=>20, 'cols'=>50, 'class'=>'form-control tinytext')); ?>
				<?php echo $form->error($model,'contents'); ?>
			</div>
		</div>
		</div>
		<div class="col-sm-4">
			<div id="searchcontrol" style="padding: 10px; max-height: 500px; overflow: auto;"></div>
		</div>
	</div>


	<div class="form-group">
		<?php echo $form->labelEx($model,'tags', array('class'=>'col-sm-2 control-label')); ?>
		<div class="col-sm-10">
			<?php echo $form->textField($model,'tags', array('class'=>'form-control')); ?>
			<?php echo $form->error($model,'tags'); ?>
		</div>
	</div>

	<div class="form-group">
		<?php echo $form->labelEx($model,'theme',array('class'=>'col-sm-2 control-label')); ?>
		<div class="col-sm-10">
			<?php echo $form->dropDownList($model,'theme',ClassPlan::$themes, array('class'=>'form-control')); ?><br />
			<?php echo CHtml::image(Yii::app()->request->baseUrl ."/css/templates/{$model->theme}.png", 'Tema', array('class'=>"img-responsive img-thumbnail", 'id'=>'theme-thumbnail')); ?>
			<hr>
			<?php echo $form->error($model,'theme'); ?>
		</div>
	</div>

<div class="form-group">
		<div class="col-sm-10">
			<?php echo $form->hiddenField($model,'sobek_keywords',array('rows'=>20, 'cols'=>50, 'class'=>'form-control')); ?>
			<?php echo CHtml::submitButton('Salvar e Publicar', array('class'=>'btn btn-default pull-right', 'onclick'=>"$('#ClassPlan_released').val(1)")); ?>
			
		</div>
		<div class="col-sm-2">
			<?php echo CHtml::submitButton('Apenas Salvar', array('class'=>'btn btn-default', 'onclick'=>"$('#ClassPlan_released').val(0)")); ?>
		</div>
</div>

</div>
</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
