<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 26.09.13
 * Time: 13:34
 * To change this template use File | Settings | File Templates.
 */

/*
 * компонент для проверки в тексте пунктуации
 * а также запуск сервера, для проверки пунктуации+ отправка и обработка запросов с этого сервера
 */

class PunctuationComponent extends CApplicationComponent{

    public $pathToCatalog;//путь к каталогу где лежат файлы джава, для запуска и работы сервера пунктуации

    // IP сервера, на котором крутится система проверки пунктуации
    public $ipServer;

    public $portServer;

    //частотник слов исключений, по которым не считаем ошибки
    public $listException = array();

    public $error = false;
    public $errorText;

    // список ошибок после проверки текста на пунктуацию
    public $punctuationErrors = array();

    /*
        * основной метод проверки текста, он запускаем все необходимые этапы
     * $listException - список исключений который мы используем для фильтрации ошибок по тексту
     * т.е.  - формируем частотный словарь(слово-форм) по списку исключений  и используем его при фильтрации по ошибкам в словах
        */
    public function checkText($text, $listException=array()){

        // формируем частотник для слов исключений
        if(!empty($listException)){
            $this->getExceptionList($listException);
        }

        // отправляем текст на проверку на сервер
        $this->sendTextToServer($text);

        //фильтруем список ошибок по словам исключениям
        $this->filteringErrorsList($text);


    }

    /*
     * обрабатываем список исключений из проекта, и фильтруем слова из ошибок, чтобы в список ошибок не попадали исключения
     */
    public function filteringErrorsList($text){

        //TODO доделать фильтрацию по частотному словарю исключений
        // массив отфильтрованных ошибок - в которых нет исключений по словам указанных админом
        $filteringPunctuationList = array();

        // перебираем список найденных ошибок от сервера пунктуации
        foreach($this->punctuationErrors as $errorWord){

            //ошибки только пунктуационные, т.е. не верно указано слово
            if($errorWord['locqualityissuetype']=='misspelling'){

                //в котором сервер ПУНКТУАЦИИ, считает что есть ошибка
                $findWord = mb_substr($text, $errorWord['offset'], $errorWord['errorlength'],'UTF-8');

                echo $findWord.'<br>';
                echo '<pre>'; print_r($errorWord);
            }
        }
    }

    /*
     *  формируем частотник для слов исключений
     * т.е. получаем для каждого слова-слово-форму
     */
    public function getExceptionList($listException){

        // подключаем компонент для определения слово-форм отдельно для каждого предложения
        $sickness = Yii::app()->sickness;

        $list_words = $sickness->baseFormForWord($listException);

        $sequenceKeys = Yii::app()->sequenceKeys;

        $this->listException =  $sequenceKeys::indexIntoValueArray($list_words);
    }

    /*
     * метод для запуска сервера, для проверки текста на пунктуацию
     */
    public function serverStart(){

        $command = 'java -cp '.$this->pathToCatalog.'/languagetool-server.jar org.languagetool.server.HTTPServer --port '.$this->portServer;

        //  выполняем команду запуска сервера ПУНКТУАЦИИ
        $res = shell_exec($command);

    }

    public function init(){

        $this->pathToCatalog = Yii::getPathOfAlias('webroot.protected.components.check.LanguageTool');

        parent::init();
    }

    /*
     * отправляем текст на проверку на сервер пунктуации
     */
    public function sendTextToServer($text){

        $postText = 'language=ru-RU&text='.$text.'&profile=true';

        try {
            $curl = curl_init();

            if (FALSE === $curl)
                throw new Exception('failed to initialize');

            curl_setopt($curl, CURLOPT_URL, $this->ipServer);
            curl_setopt($curl, CURLOPT_PORT, $this->portServer);
            curl_setopt($curl, CURLOPT_SSLVERSION, 3);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postText);
            //теперь curl вернет нам ответ, а не выведет
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_REFERER, 'http://localhost');
            $result = curl_exec($curl);

            // Check if any error occurred
            if(curl_errno($curl))
            {
                echo 'Curl error: ' . curl_error($curl).'<br>'; die();
            }


            curl_close($curl);

            if (FALSE === $result)
                throw new Exception(curl_error($curl), curl_errno($curl));

            // ...process $content now
        } catch(Exception $e) {

            trigger_error(sprintf(
                    'Curl failed with error #%d: %s',
                    $e->getCode(), $e->getMessage()),
                E_USER_ERROR);

        }

        // парсим ответ от сервера и приводим его к виду массива
        $xml = simplexml_load_string($result);

        // из XML формируем массив
        $array = MyText::simpleXMLToArray($xml);

        $this->punctuationErrors = $array;
    }
}