<?php
$this->breadcrumbs=array(
	UserModule::t('Users')=>array('/user'),
	UserModule::t('Manage'),
);

$this->menu=array(
    array('label'=>UserModule::t('Create User'), 'url'=>array('create')),
    array('label'=>UserModule::t('Manage Users'), 'url'=>array('admin')),
    array('label'=>UserModule::t('Manage Profile Field'), 'url'=>array('profileField/admin')),
    array('label'=>UserModule::t('List User'), 'url'=>array('/user')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
});	
$('.search-form form').submit(function(){
    $.fn.yiiGridView.update('user-grid', {
        data: $(this).serialize()
    });
    return false;
});
");

?>
<h2><?php echo UserModule::t("Manage Users"); ?></h2>

<!--<p>--><?php //echo UserModule::t("You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b> or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done."); ?><!--</p>-->

<?php
    // для супер-админа есть доступ на добавление пользователей
    if(Yii::app()->user->role == User::ROLE_SA_ADMIN){
        echo CHtml::link('Добавить пользователя','/user/admin/create');
    }
     //echo CHtml::link(UserModule::t('Advanced Search'),'#',array('class'=>'search-button'));
?>
<!--<div class="search-form" style="display:none">-->
<?php //
//$this->renderPartial('_search',array(
//    'model'=>$model,
//));
//    ?>
<!--</div><!-- search-form -->

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
    'template'=>'{items}{pager}',
	'columns'=>array(
		array(
			'name' => 'id',
			'type'=>'raw',
			'value' => 'CHtml::link(CHtml::encode($data->id),array("admin/update","id"=>$data->id))',
		),
		array(
			'name' => 'username',
			'type'=>'raw',
			'value' => 'CHtml::link(UHtml::markSearch($data,"username"),array("admin/view","id"=>$data->id))',
		),
//		array(
//			'name'=>'email',
//			'type'=>'raw',
//			'value'=>'CHtml::link(UHtml::markSearch($data,"email"), "mailto:".$data->email)',
//		),
		//'create_at',
		//'lastvisit_at',
		array(
			'name'=>'role',
			'value'=>' UserModule::t($data->role)',
			'filter'=>User::rolesList(),
		),
		array(
			'name'=>'status',
			'value'=>'User::itemAlias("UserStatus",$data->status)',
			'filter' => User::itemAlias("UserStatus"),
		),
        array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
        ),
	),
)); ?>
