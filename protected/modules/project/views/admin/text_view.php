<div class="form">
    <h3>Информация о задании:</h3>
<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'text-form',
	'enableAjaxValidation'=>false,
)); ?>
    <!-- цикл по полям со значениями, кроме ключевых слов и сведений, ключевики выводим отдельно с отдельном диве, для скрывания и ссылка для скачивания ключевиков -->
    <?php
    $formElements = '';
    // список сведений - обработанных ключевиков
    $reductions = array();
    $keyWords = '<div id="tbl_key_words" style="width: 750px; overflow: auto; height: 165px; border: 1px solid;"><table border="1">';
    $counterKeyWords = 0;

    foreach($data as $i=>$row){
        //формируем элементы формы ключевики выводим сгруппировано
        if($row['import_var_id']==Yii::app()->params['key_words']){

            $counterKeyWords++;

            $input = CHtml::textField('ImportVarsValue['.$row['id'].']',$row['import_var_value'], array('style'=>'width:300px'));

            $del_link = CHtml::ajaxLink('Удалить',
                Yii::app()->createUrl('/project/admin/deletekeyword', array('id'=>$row['id'])),
                array( // ajaxOptions
                  'type' => 'POST',
                  'beforeSend' => "function( request )
                   {
                     // Set up any pre-sending stuff like initializing progress indicators
                   }",
                  'success' => "function(data)
                    {
                      // handle return data
                      //alert( data );
                      jQuery('#tbl_key_words').html(data);
                    }",
                  'data' => array('id' => $row['id'],'textId'=>$model->id)
                ),
                array( // самое интересное
                    //'href' => Yii::app()->createUrl( '/project/admin/deletekeyword' ),// подменяет ссылку на левую
                    'class' => "sadfsadfsadclass" // добавляем какой-нить класс для оформления
                )
            );
            $keyWords.='<tr class="del_link"><td><div class="row"><label for="'.$row['title'].'">Ключевое слово</label>'.$input.$del_link.'</div></td></tr>';
        }elseif($row['import_var_id']==Yii::app()->params['reduction']){
            if(!empty($row['import_var_value'])){
                $reductions[] = $row['import_var_value'];
            }
        }else{
            $input = CHtml::textField('ImportVarsValue['.$row['id'].']',$row['import_var_value']);
            $formElements.='<div class="row"><label for="'.$row['title'].'">'.$row['title'].'</label>'.$input.'</div>';
        }
    }
    $keyWords.='</table></div> ';
    echo $formElements;
    ?>
    <?php // если есть ключевые слова по тексту - выводим их
        if($counterKeyWords!=0){
            echo $keyWords;
        }
        // если в задании есть "СВЕДЕНИЯ" - выводим их на экран в ввиде списка
        if(!empty($reductions)){
            $select_reduction = CHtml::dropDownList('reductions_form','',$reductions, array('size'=>10, 'style'=>'width:500px;'));
            echo '<div class="row"><label for="сведения">Сведения</label>'.$select_reduction.'</div>';//$reductions
        }
     ?>

    <div class="row">
   		<?php echo $form->hiddenField($model,'project_id'); ?>
        <?php echo $form->hiddenField($model,'status'); ?>
        <?php echo $form->hiddenField($model,'id'); ?>
   	</div>
    <div class="row buttons">
   		<?php
           $this->widget('bootstrap.widgets.TbButton',array(
           	'label' => $model->isNewRecord ? 'Добавить' : 'Принять задание',
            'buttonType'=>'submit',
           	'type' => 'submit',
           	'size' => 'large'
           ));
        echo CHtml::link('Отклонить задание',
            '#',
            array(
                'data-toggle'=>'modal',
                'data-target'=>'#rejectProject',
                'style'=>'margin-left:50px;'
            )
        );
           ?>
   	</div>
    <?php
    $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'rejectProject')); ?>
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h4>Отклонить задание:</h4>
    </div>
    <div class="modal-body">
        <?php $this->renderPartial('reject', array('reject'=>$reject));?>
    </div>
    <?php $this->endWidget();

    ?>
<?php $this->endWidget(); ?>
    <div class="row">
        <?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'myModal')); ?>
            <div class="modal-header">
                    <a class="close" data-dismiss="modal">×</a>
                    <h4>Добавить новое ключевое слово</h4>
                <div id="error_add" style="font-size: large; color: red"></div>
            </div>
            <div class="modal-body">
                <?=CHtml::beginForm('','post',array('enctype'=>'multipart/form-data','id'=>'frmNewKeyWord','name'=>'editForm')); ?>
                <?=CHtml::hiddenField("textId",$model->id,array('id'=>'catId')); ?>
                <?=Yii::t('lan','Введите ключевое слово');?>
                <?=CHtml::textField("keyWordNew","",array('id'=>'keyWordNew')); ?><br>
                <?=CHtml::endForm(); ?>
            </div>
            <?=CHtml::ajaxSubmitButton(Yii::t('lan','Добавить'),
                                    '',
                                    array('type' => 'POST',
                                          //'update' => '#tbl_key_words',
                                          'data'=>'js:jQuery("#frmNewKeyWord").serialize()',
                                          'success' => 'function(a){
                                                jQuery("#tbl_key_words").html(a);
                                                alert("Успешно добавили ключевое слово");
                                          }',
                                          'error' => 'function(a){ alert("Ошибка обработки запроса"); }'
                                         ),
                                    array('type' => 'submit',
                                            'style'=>'margin-left:40px;',
                                          'id'=>'btnEditCategory',
                                          'class'=>'btn btn-primary',
                                          'buttonType'=>'submit',
                                          //'data-toggle'=>"modal",
                                          'data-target'=>"#myModal",
                                          'data-dismiss'=>'modal',
                                         )
                                  ); ?>
        <?php $this->endWidget(); ?>
    </div>

    <a onclick="javascript:editCategory" data-toggle="modal" data-target="#myModal" style="color:green;font-size:8pt;text-decoration:none;border-bottom: 1px green dashed;" id="editCategory" href="javascript:void(0)">Добавить ключевое слово</a>
</div><!-- form -->
<?php $this->widget('CommentsWidget',array('model_id'=>$model->id, 'model'=>get_class($model))); ?>
<script type="text/javascript">
    $('a.del_link')
</script>