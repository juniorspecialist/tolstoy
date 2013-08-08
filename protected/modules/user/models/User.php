<?php

class User extends CActiveRecord
{
	const STATUS_NOACTIVE=0;
	const STATUS_ACTIVE=1;
	const STATUS_BANNED=-1;

    // роли пользователей системы
    const ROLE_SA_ADMIN = 'super_administrator';
    const ROLE_ADMIN = 'administrator';
    const ROLE_EDITOR = 'editor';
    const ROLE_COPYWRITER = 'copywriter';

	
	//TODO: Delete for next version (backward compatibility)
	//const STATUS_BANED=-1;
	
	/**
	 * The followings are the available columns in table 'users':
	 * @var integer $id
	 * @var string $username
	 * @var string $password
	 * @var string $email
	 * @var string $activkey
	 * @var integer $createtime
	 * @var integer $lastvisit
	 * @var integer $superuser
	 * @var integer $status
     * @var timestamp $create_at
     * @var timestamp $lastvisit_at
	 */

	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return Yii::app()->getModule('user')->tableUsers;
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.CConsoleApplication
		return ((get_class(Yii::app())=='CConsoleApplication' || (get_class(Yii::app())!='CConsoleApplication' && Yii::app()->getModule('user')->isAdmin()))?array(
			array('username', 'length', 'max'=>20, 'min' => 3,'message' => UserModule::t("Incorrect username (length between 3 and 20 characters).")),
			array('password', 'length', 'max'=>128, 'min' => 4,'message' => UserModule::t("Incorrect password (minimal length 4 symbols).")),
		    //array('email', 'email'),
			array('username', 'unique', 'message' => UserModule::t("This user's name already exists.")),
			//array('email', 'unique', 'message' => UserModule::t("This user's email address already exists.")),
			array('username', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u','message' => UserModule::t("Incorrect symbols (A-z0-9).")),
			array('status', 'in', 'range'=>array(self::STATUS_NOACTIVE,self::STATUS_ACTIVE,self::STATUS_BANNED)),
			array('superuser', 'in', 'range'=>array(0,1)),
            array('role', 'length', 'max'=>40),
            array('create_at', 'default', 'value' => date('Y-m-d H:i:s'), 'setOnEmpty' => true, 'on' => 'insert'),
            array('lastvisit_at', 'default', 'value' => '0000-00-00 00:00:00', 'setOnEmpty' => true, 'on' => 'insert'),
			array('username,  superuser, status, role', 'required'),
			array('superuser, status, rate', 'numerical', 'integerOnly'=>true),
			array('id, username, password,  activkey, create_at, lastvisit_at, superuser, status, rate', 'safe', 'on'=>'search'),
		):((Yii::app()->user->id==$this->id)?array(
			array('username', 'required'),
			array('username', 'length', 'max'=>20, 'min' => 3,'message' => UserModule::t("Incorrect username (length between 3 and 20 characters).")),
			//array('email', 'email'),
			array('username', 'unique', 'message' => UserModule::t("This user's name already exists.")),
			array('username', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u','message' => UserModule::t("Incorrect symbols (A-z0-9).")),
			//array('email', 'unique', 'message' => UserModule::t("This user's email address already exists.")),
		):array()));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
        $relations = Yii::app()->getModule('user')->relations;
        if (!isset($relations['profile']))
            $relations['profile'] = array(self::HAS_ONE, 'Profile', 'user_id');
        return $relations;
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => UserModule::t("Id"),
			'username'=>UserModule::t("username"),
			'password'=>UserModule::t("password"),
			'verifyPassword'=>UserModule::t("Retype Password"),
			//'email'=>UserModule::t("E-mail"),
			'verifyCode'=>UserModule::t("Verification Code"),
			'activkey' => UserModule::t("activation key"),
			'createtime' => UserModule::t("Registration date"),
			'create_at' => UserModule::t("Registration date"),
			'lastvisit_at' => UserModule::t("Last visit"),
			'superuser' => UserModule::t("Superuser"),
			'status' => UserModule::t("Status"),
            'role'=>'Набор прав',
            'rate'=>'Ставка(за 1К знаков)',
		);
	}
	
	public function scopes()
    {
        return array(
            'active'=>array(
                'condition'=>'status='.self::STATUS_ACTIVE,
            ),
            'notactive'=>array(
                'condition'=>'status='.self::STATUS_NOACTIVE,
            ),
            'banned'=>array(
                'condition'=>'status='.self::STATUS_BANNED,
            ),
            'superuser'=>array(
                //'condition'=>'superuser=1',
                'condition'=>'role="'.self::ROLE_SA_ADMIN.'"',
            ),
            'superadmin'=>array(
                'condition'=>'role="'.self::ROLE_SA_ADMIN.'"',
            ),
            'admin'=>array(
                'condition'=>'role="'.self::ROLE_ADMIN.'"',
            ),
            'all_admin'=>array(
                'condition'=>'role="'.self::ROLE_ADMIN.'" OR role="'.self::ROLE_SA_ADMIN.'"',
            ),
            'editor'=>array(
                'condition'=>'role="'.self::ROLE_EDITOR.'"',
            ),

            'notsafe'=>array(
            	'select' => 'id, username, password, activkey, create_at, lastvisit_at, superuser, status, role, rate',
            ),
        );
    }
	
	public function defaultScope()
    {
        return CMap::mergeArray(Yii::app()->getModule('user')->defaultScope,array(
            'alias'=>'user',
            'select' => 'user.id, user.username, user.create_at, user.lastvisit_at, user.superuser, user.status, user.role, user.rate',
        ));
    }
	
	public static function itemAlias($type,$code=NULL) {
		$_items = array(
			'UserStatus' => array(
				self::STATUS_NOACTIVE => UserModule::t('Not active'),
				self::STATUS_ACTIVE => UserModule::t('Active'),
				self::STATUS_BANNED => UserModule::t('Banned'),
			),
			'AdminStatus' => array(
				'0' => UserModule::t('No'),
				'1' => UserModule::t('Yes'),
			),
		);
		if (isset($code))
			return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
		else
			return isset($_items[$type]) ? $_items[$type] : false;
	}
	
/**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;
        
        $criteria->compare('id',$this->id);

        $criteria->compare('username',$this->username,true);
        $criteria->compare('password',$this->password);
        //$criteria->compare('email',$this->email,true);
        $criteria->compare('activkey',$this->activkey);
        $criteria->compare('create_at',$this->create_at);
        $criteria->compare('lastvisit_at',$this->lastvisit_at);
        $criteria->compare('superuser',$this->superuser);
        $criteria->compare('status',$this->status);

        // если роль не указана, используем условие, выбираем всех, кроме КОПИРАЙТОРОВ
        if(empty($this->role)){
            $criteria->condition = 'role!="'.self::ROLE_COPYWRITER.'"';
        }else{
            $criteria->compare('role',$this->role);
        }

        return new CActiveDataProvider(get_class($this), array(
            'criteria'=>$criteria,
        	'pagination'=>array(
				'pageSize'=>Yii::app()->getModule('user')->user_page_size,
			),
        ));
    }

    public function getCreatetime() {
        return strtotime($this->create_at);
    }

    public function setCreatetime($value) {
        $this->create_at=date('Y-m-d H:i:s',$value);
    }

    public function getLastvisit() {
        return strtotime($this->lastvisit_at);
    }

    public function setLastvisit($value) {
        $this->lastvisit_at=date('Y-m-d H:i:s',$value);
    }

    /*
     *  список ролей пользователей
     */
    public static function rolesList($all = false){
        if($all){
            return array(
                User::ROLE_SA_ADMIN => UserModule::t(User::ROLE_SA_ADMIN),
                User::ROLE_ADMIN => UserModule::t(User::ROLE_ADMIN),
                User::ROLE_EDITOR => UserModule::t(User::ROLE_EDITOR),
                User::ROLE_COPYWRITER => UserModule::t(User::ROLE_COPYWRITER)
            );

        }else{
            return array(
                User::ROLE_SA_ADMIN => UserModule::t(User::ROLE_SA_ADMIN),
                User::ROLE_ADMIN => UserModule::t(User::ROLE_ADMIN),
                User::ROLE_EDITOR => UserModule::t(User::ROLE_EDITOR),

            );
        }
    }

    /*
     * на основании случайного логина и пароля - создаём юзера, копирайтора
     * $project_id - к какому проекту подвязываем исполнителя
     */
    public static function createCopywriter($project_id){
        // генерируем случайный логин и пароль для юзера
        for($i=0;$i<150;$i++){// 50 попыток получить уникальный логин

            // формируем случайный логин-ник
            $rnd_login = MyText::rndString(rand(4,12),'all');
            $rnd_pass = MyText::rndString(rand(3,6),'all');

            // проверяем на уникальность случайный логин
            $sql = 'SELECT id
                    FROM {{users}}
                    WHERE username="'.$rnd_login.'"';

            $find = Yii::app()->db->createCommand($sql)->queryRow();

            // если нет данных, значит - логин уникальный
            if(empty($find)){
                break;
            }
        }

        //создаём доступы для копирайтора, по проекту
        $model=new User;
        $profile=new Profile;

        $model->username = $rnd_login;
        $model->role = self::ROLE_COPYWRITER;
        $model->status = 1; // активированный пользователь
        //$model->activkey=Yii::app()->controller->module->encrypting(microtime().$rnd_pass);
        $model->activkey = UserModule::encrypting(microtime().$rnd_pass);
        //$profile->attributes=$_POST['Profile'];
        //$profile->user_id=0;
        //$model->password=Yii::app()->controller->module->encrypting($rnd_pass);
        $model->password = UserModule::encrypting($rnd_pass);
        if($model->save()) {
            // указываем ему имя и фамилию
            $profile->lastname = MyText::rndString(rand(4,12),'all');
            $profile->firstname = MyText::rndString(rand(4,16),'all');
            $profile->user_id=$model->id;
            $profile->save();
            // подвязка пользователя к проекту
            $projectUser = new ProjectUsers();
            $projectUser->project_id = $project_id;
            $projectUser->user_id = $model->id;
            $projectUser->save();
        }else{
            //print_r($model->errors);die();
        }

        return array('login'=>$rnd_login, 'pass'=>$rnd_pass);
    }

    /*
     * находим редактора, у которого меньше всего проектов в работе
     * и на него будем вешать новый проект, подвязывать
     */
    public static function getFreeRedactor(){
        //GROUP BY  tbl_users.id
        $sql = 'SELECT COUNT(tbl_project.id) as count, tbl_users.id
        FROM `tbl_project_users` , tbl_users,tbl_project
        WHERE tbl_project.status="'.Project::CREATE_TASK.'"
           AND tbl_project.id=tbl_project_users.project_id
           AND tbl_users.id=tbl_project_users.user_id
           AND tbl_users.role="'.User::ROLE_EDITOR.'"
            GROUP BY  tbl_users.id
           ORDER BY count ASC';

        //echo $sql.'<br>';
        $data = Yii::app()->db->createCommand($sql)->queryRow();

        // не нашли данных, - находим первого попавшегося РЕДАКТОРА и возращаем его
        if(empty($data['id'])){
            $sqlFindRedactor = 'SELECT id
                                FROM tbl_users
                                WHERE role="'.User::ROLE_EDITOR.'"
                                ORDER BY id ASC';
            $redactorFirst = Yii::app()->db->createCommand($sqlFindRedactor)->queryRow();
            return $redactorFirst['id'];
        }

        return $data['id'];
    }

    /*
     * определяем является ли текущий пользователь АДМИН или СУПЕР_АДМИНОМ
     */
    static function isAdmin(){
        if(Yii::app()->user->isGuest){
            return false;
        }else{
            if(Yii::app()->user->role==User::ROLE_ADMIN || Yii::app()->user->role==User::ROLE_SA_ADMIN){
                return true;
            }
        }

        return false;
    }
}