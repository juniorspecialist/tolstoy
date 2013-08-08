<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Саня
 * Date: 17.07.13
 * Time: 17:07
 * To change this template use File | Settings | File Templates.
 */
 
class UniqueComponent extends CApplicationComponent {

    // регион поиска в Яндексе
    public $ya_region;

    // процент уникальности текста, после обработки результатов из поисковика и сравнения текста с результатами из ПС
    public $unique_percent;

    public $links_list = array(); // список ссылок найденных в поисковике, выдача ПС

    // метод для определения уникальности текста
    //$text - текст который необходимо проверить на уникальность
    public function runCheck($text){
        /*берешь текст
        бьешь на части по 500 символов
        из части берешь 3 разных куска размером в Х шинглов (шинглы настраиваются где-то в конфиге)
        чистишь от всего, кроме точек
        вбиваешь в яндекс, причем вбиваешь через оператор ||
         */

        // компонент для анализа и обработки текста
        $shingle = Yii::app()->shingle;

        // получаем список частей(по тексту)+по каждой части - массив шинглов, заданного размера
        $list_parts = $shingle->get3Parts($text);

        // список ссылок, которые мы нашли из выдачи поисковика
        $links_list = array();

        // по каждой части делаем запрос к яндексу, где указываем 3 разных шингла
        //array('part_text'=>$part_text, 'shingles_list'=>$list_shingle);
        foreach($list_parts as $l=>$row){
            file_put_contents('log/'.$l.'|по каждой части делаем запрос к яндексу.txt','');
            // получаем список источников, которые выдал нам Яндекс при запросе с шинглами
            $this->parseYandex($row['shingles_list']);
            //break;
        }

        // список содержимого страничек по найденным ссылкам из выдачи ПС
        $list_contents_pages = array();

        file_put_contents('log/'.'по списку полученных ссылок получаем страницы с определнием кодировки.txt','');
        // по списку полученных ссылок получаем страницы с определнием кодировки
        foreach($this->links_list as $k=>$link_page){

            // возможно мы нашли ссылку на какой-то конкретный файл - его пропускаем
            $find_file = pathinfo($link_page);
            if(!empty($find_file['extension'])){ continue;}

            $content_page = $this->curlBehavior->get($link_page);

            file_put_contents('log/'.'получаем страницу_'.$k.'.txt',$link_page);

            // преобразовываем текст в кодировку UTF-8
            if(!empty($content_page)){
                file_put_contents('log/convert_url_'.$k.'.txt',$link_page);
                $list_contents_pages[] = Encoding::toUTF_8($content_page, $link_page);
            }
        }

        $this->analyzeTexts($text, $list_contents_pages);
    }

    /*
     * сравниваем исходный текст с текстами полученными из ПС
     * и по каждому из текстов определяем процент совпадения
     * $contents_list - список содержимого страничек из ПС
     * $text - исходный текст, с которым будем сравнивать
     */
    public function analyzeTexts($text, $contents_list){

        $textCompare = Yii::app()->shingle;

        //Устанавливаем длину шингла в два слова
        //$textCompare->shinglePrice = 2;

        file_put_contents('log/'.'сравниваем исходный текст с текстами полученными из ПС.txt','');

        $incomingTextShingles = $textCompare->getShingle($text);

        $unique_all = 0;

        foreach($contents_list as $k=>$site_page){
            $similarity = Yii::app()->shingle->checkText($incomingTextShingles, $site_page);

            echo $similarity.'<br>'; //в данном случае результат будет 100
            //echo 'k='.$k.'|уникальность='.$textCompare->checkText($textCompare->getShingle($text), $textCompare->getShingle($site_page)).'<br>';
            $unique_all=+$similarity;
        }

        echo 'Уникальность текста='.$unique_all.'<br>';

    }

    /*
     * запрос к яндексу для получения списка ссылок по 3 разным кускам шинглов по тексту
     */
    public function parseYandex($shingle_list){
        // формируем строку для отправки запроса к яндексу
        //("шингл 1") | ("шингл 2")| ("шингл 3")
        $url_shingle = '("'.$shingle_list[0].'")|("'.$shingle_list[1].'")|("'.$shingle_list[2].'")';

        // используем поведение, для отправки запроса к яндексу, через прокси
        // страница результатов яндекса - выдача ПС

        $header = array(
            'Accept'=>'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Encoding:'=>'gzip,deflate,sdch',
            'Accept-Language'=>'ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4',
            'Cache-Control'=>'max-age=0',
            'Connection'=>'keep-alive',
            'Host'=>'www.yandex.ru',
        );

        $ya_page = $this->
            curlBehavior->
            get('http://yandex.ru/yandsearch?lr='.$this->ya_region.'&text='.urlencode($url_shingle),
                $header,
                $this->getRndProxy(),
                $this->curlBehavior->proxy_login,
                $this->curlBehavior->proxy_pass
            );

        file_put_contents('log/'.'парсим выдачу яндекса и находими список ссылок на ресурсы.txt',$ya_page);

        //echo $ya_page.'<br>';

        /*
         * парсим выдачу яндекса и находими список ссылок на ресурсы
         */
        preg_match_all('/<\/b><a class="b-serp-item__title-link" href="(.*?)" onmousedown/si',$ya_page, $links_yandex_resource);

        echo '<pre>'; print_r($links_yandex_resource[1]);

        //$links_yandex_resource[1] - массив сссылок на результаты запроса из яндекса

        //TODO дописать проверку на "Искомая комбинация слов нигде не встречается" в поиске яндекса, чтобы знать что по данному шинглу - 100% уникальность

        // записываем в общий массив найденных урлов по тексту - ссылки на наденные статьи из ПС
        foreach($links_yandex_resource[1] as $i=>$url){
            $this->links_list[]  = $url;
        }
    }
}
