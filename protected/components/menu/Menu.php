<?php
    Yii::import('zii.widgets.CPortlet');

/* класс для отображения пользовательского меню в системе
для каждой группы пользователей - своё меню
т.е. для каждой группы своё представления для меню
*/
class Menu extends CPortlet
{
    public function init()
    {
        //$this->title=CHtml::encode(Yii::app()->user->name);
        parent::init();
    }

    /*
     * если пользователь авторизирован выводим по его роле нужное ему меню
     * если пользователь - гость - выводим стандартное меню "авторизации"
     */
    protected function renderContent(){

        // если пользователь гость
        if(Yii::app()->user->isGuest){

            $this->render('guest');

        }else{
            //echo Yii::app()->user->role.'|'. User::ROLE_SA_ADMIN;
            // пользователь - авторизирован - определяем его РОЛЬ

            if(Yii::app()->user->role == User::ROLE_SA_ADMIN){// если пользователь супер-админ

                $this->render('application.components.menu.views.sa_admin');
            }
            if(Yii::app()->user->role == User::ROLE_ADMIN){// если пользователь админ
                $this->render('application.components.menu.views.admin');
            }
            if(Yii::app()->user->role == User::ROLE_EDITOR){// если пользователь редактор
                $this->render('application.components.menu.views.editor');
            }
            if(Yii::app()->user->role == User::ROLE_COPYWRITER){// если пользователь копирайтер
                $this->render('application.components.menu.views.copywriter');
            }
        }

//        $js = Yii::app()->clientScript;
//        //подключаем свои js скрипты
//        $js->registerScriptFile(Yii::app()->request->baseUrl.'/js/cron.js', CClientScript::POS_END);
//
//        Yii::app()->clientScript->registerCssFile('/css/modal.css');

        //$this->render('adminMenu');
    }

    function isActive($route)
    {
        $routeCurrent = '';
        if (Yii::app()->controller->id !== null) {
            $routeCurrent .= sprintf('%s/', Yii::app()->controller->id);
        }
        $routeCurrent .= Yii::app()->controller->action->id;

        //echo $routeCurrent.'<br>';

        if($routeCurrent==$route){
            return true;
        }

        return false;
    }
}