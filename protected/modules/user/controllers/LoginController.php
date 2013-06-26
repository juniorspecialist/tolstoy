<?php

class LoginController extends Controller
{
	public $defaultAction = 'login';

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		if (Yii::app()->user->isGuest) {
			$model=new UserLogin;
			// collect user input data
			if(isset($_POST['UserLogin']))
			{
				$model->attributes=$_POST['UserLogin'];
				// validate user input and redirect to previous page if valid
				if($model->validate()) {
					$this->lastViset();
					if (Yii::app()->user->returnUrl=='/index.php')
						$this->redirect(Yii::app()->controller->module->returnUrl);
					else
						$this->redirect(Yii::app()->user->returnUrl);
				}
			}
			// display the login form
			$this->render('/user/login',array('model'=>$model));
		} else
			$this->redirect(Yii::app()->controller->module->returnUrl);
	}
	
	private function lastViset() {
		$lastVisit = User::model()->notsafe()->findByPk(Yii::app()->user->id);
		$lastVisit->lastvisit = time();
		$lastVisit->save();
        // если КОПИРАЙТОР входит ВПЕРВЫЕ в систему, тогда изменим статус проекта к котор. он подвязан
        if($lastVisit->role==User::ROLE_COPYWRITER){
            $sql = 'SELECT {{project}}.id AS id
                    FROM {{project_users}}, {{project}}
                    WHERE {{project_users}}.user_id="'.$lastVisit->id.'"
                        AND {{project_users}}.project_id={{project}}.id
                        AND {{project}}.status="'.Project::CREATE_TASK.'"';
            $project = Yii::app()->db->createCommand($sql)->queryRow();
            if(!empty($project)){
                Yii::app()->db->createCommand('UPDATE {{project}}
                                               SET status="'.Project::PERFORMER.'"
                                               WHERE status="'.Project::CREATE_TASK.'"
                                                    AND id="'.$project['id'].'"')
                                                ->execute();
            }
        }

	}

}