<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
?>

 <div class="page-header">
        <div class="row">
          <div class="col-lg-6">
            <h1>Projetos</h1>
            <p class="lead">Crie seus projetos de forma fácil e rápida</p>
          </div>

          <div class="col-lg-6">
          	<?php if(isset($this->breadcrumbs)):?>
				<?php $this->widget('zii.widgets.CBreadcrumbs', array(
					'links'=>$this->breadcrumbs,
				)); ?><!-- breadcrumbs -->
			<?php endif?>
		</div>
        </div>
      </div>
