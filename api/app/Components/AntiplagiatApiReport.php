<?php


namespace App\Components;


class AntiplagiatApiReport
{
    private $data;

    /**
     * AntiplagiatReport constructor.
     * @param $data
    {#230
     * +"GetReportViewResult": {#231
     * +"DocId": {#233
     * +"Id": 687
     * +"External": null
     * }
     * +"Summary": {#232
     * +"ReportNum": 1
     * +"ReadyTime": "2016-11-05T22:08:31.508703"
     * +"Score": 61.5060234
     * +"ReportWebId": "/Report/ByLink/ApiCorp/674?v=1&userId=4&validationHash=F4B1DE9CEC02F1301212F804CEF5783EAD47ABC5"
     * +"ReadonlyReportWebId": "/Report/ByLink/674?v=1&userId=4&validationHash=10C6A98BE80F36FE4985D628B65BF184D938FA71"
     * +"ShortReportWebId": "/Report/ByLink/Short/674?v=1&userId=4&validationHash=14F0D1BE6359B9B5FE9314FE9F9781B33DE05F83"
     * }
     * +"Stats": {#220
     * +"TextSize": 1660
     * +"Language": "RU"
     * +"MainWordCount": 172
     * +"OtherWordCount": 27
     * +"SentenceCount": 10
     * }
     * +"Attributes": {#210
     * +"Name": "685-3114-1-SM.txt"
     * +"Url": ""
     * +"Author": ""
     * +"Custom": array:1 [
     * 0 => {#227
     * +"AttrName": "DetectedLanguages"
     * +"AttrValue": "ru"
     * }
     * ]
     * }
     * +"CheckServiceResults": array:2 [
     * 0 => {#228
     * +"CheckServiceName": "testapi3"
     * +"CollectionDescription": null
     * +"ScoreByReport": {#234
     * +"Plagiarism": 0.0
     * +"Legal": 0.0
     * }
     * +"ScoreByCollection": {#235
     * +"Plagiarism": 0.0
     * +"Legal": 0.0
     * }
     * +"CloseDate": "2016-11-05T22:08:31.149509"
     * }
     * 1 => {#236
     * +"CheckServiceName": "wikipedia"
     * +"CollectionDescription": null
     * +"ScoreByReport": {#237
     * +"Plagiarism": 61.5060234
     * +"Legal": 0.0
     * }
     * +"ScoreByCollection": {#238
     * +"Plagiarism": 61.50602
     * +"Legal": 0.0
     * }
     * +"CloseDate": "2016-11-05T22:08:31.508703"
     * +"Sources": array:3 [
     * 0 => {#239
     * +"SrcHash": 72341212203333969
     * +"DocId": null
     * +"Name": "Огурец обыкновенный"
     * +"Url": "http://ru.wikipedia.org/wiki/Огурец обыкновенный"
     * +"Author": null
     * +"ScoreBySource": 26.6265068
     * +"ScoreByReport": 26.6265068
     * +"TimeStamp": null
     * }
     * 1 => {#240
     * +"SrcHash": 72341212203319914
     * +"DocId": null
     * +"Name": "PHP (1/2)"
     * +"Url": "http://ru.wikipedia.org/wiki/PHP#1"
     * +"Author": null
     * +"ScoreBySource": 23.8554211
     * +"ScoreByReport": 23.8554211
     * +"TimeStamp": null
     * }
     * 2 => {#241
     * +"SrcHash": 72341212203323170
     * +"DocId": null
     * +"Name": "Ruby (1/2)"
     * +"Url": "http://ru.wikipedia.org/wiki/Ruby#1"
     * +"Author": null
     * +"ScoreBySource": 11.0240965
     * +"ScoreByReport": 11.0240965
     * +"TimeStamp": null
     * }
     * ]
     * }
     * ]
     * +"Details": {#242
     * +"Text": """
     * PHP (/pi:.eɪtʃ.pi:/ англ. PHP: Hypertext Preprocessor — «PHP: препроцессор гипертекста»; первоначально Personal Home Page Tools[9] — «Инструменты для создания персональных веб-страниц») — скриптовый язык[10] общего назначения, интенсивно применяемый для разработки веб-приложений. В настоящее время поддерживается подавляющим большинством хостинг-провайдеров и является одним из лидеров среди языков, применяющихся для создания динамических веб-сайтов[11].\n
     * Язык и его интерпретатор разрабатываются группой энтузиастов в рамках проекта с открытым кодом[12]. Проект распространяется под собственной лицензией, несовместимой с GNU GPL.\n
     * Ruby (англ. ruby — рубин, произносится ['ru:bɪ] — руби) — динамический, рефлективный, интерпретируемый высокоуровневый язык программирования[7][8]. Язык обладает независимой от операционной системы реализацией многопоточности, строгой динамической типизацией, сборщиком мусора и многими другими возможностями[⇨]. По особенностям синтаксиса он близок к языкам Perl и Eiffel, по объектно-ориентированному подходу — к Smalltalk. Также некоторые черты языка взяты из Python, Lisp, Dylan и Клу.\n
     * Кроссплатформенная реализация интерпретатора языка является полностью свободной[5].\n
     * Согласно этимологическому словарю Фасмера, название заимствовано из ср.-греч. ἄγουρος (огурец), которое восходит к ἄωρος (незрелый). Этот овощ, поедаемый в незрелом виде, нарочито противопоставляется дыне — πέπων, которую едят в зрелом виде[2].\n
     * Название этого растения на санскрите созвучно с именем легендарного индийского князя Бута (слово «бута» на санскрите означает «огонь»), который имел шестьдесят тысяч детей, и связано с многосемянностью плода.
     * """
     * +"CiteBlocks": array:18 [
     * 0 => {#243
     * +"Offset": 208
     * +"Length": 139
     * +"SrcHash": 72341212203319914
     * +"Type": 1
     * }
     * 1 => {#244
     * +"Offset": 347
     * +"Length": 54
     * +"SrcHash": 72341212203319914
     * +"Type": 1
     * }
     * 2 => {#245
     * +"Offset": 401
     * +"Length": 27
     * +"SrcHash": 72341212203319914
     * +"Type": 1
     * }
     * 3 => {#246
     * +"Offset": 428
     * +"Length": 29
     * +"SrcHash": 72341212203319914
     * +"Type": 1
     * }
     * 4 => {#247
     * +"Offset": 457
     * +"Length": 80
     * +"SrcHash": 72341212203319914
     * +"Type": 1
     * }
     * 5 => {#248
     * +"Offset": 557
     * +"Length": 28
     * +"SrcHash": 72341212203319914
     * +"Type": 1
     * }
     * 6 => {#249
     * +"Offset": 585
     * +"Length": 39
     * +"SrcHash": 72341212203319914
     * +"Type": 1
     * }
     * 7 => {#250
     * +"Offset": 757
     * +"Length": 53
     * +"SrcHash": 72341212203323170
     * +"Type": 1
     * }
     * 8 => {#251
     * +"Offset": 810
     * +"Length": 33
     * +"SrcHash": 72341212203323170
     * +"Type": 1
     * }
     * 9 => {#252
     * +"Offset": 973
     * +"Length": 19
     * +"SrcHash": 72341212203323170
     * +"Type": 1
     * }
     * 10 => {#253
     * +"Offset": 1007
     * +"Length": 41
     * +"SrcHash": 72341212203323170
     * +"Type": 1
     * }
     * 11 => {#254
     * +"Offset": 1059
     * +"Length": 37
     * +"SrcHash": 72341212203323170
     * +"Type": 1
     * }
     * 12 => {#255
     * +"Offset": 1207
     * +"Length": 34
     * +"SrcHash": 72341212203333969
     * +"Type": 1
     * }
     * 13 => {#256
     * +"Offset": 1241
     * +"Length": 34
     * +"SrcHash": 72341212203333969
     * +"Type": 1
     * }
     * 14 => {#257
     * +"Offset": 1275
     * +"Length": 132
     * +"SrcHash": 72341212203333969
     * +"Type": 1
     * }
     * 15 => {#258
     * +"Offset": 1407
     * +"Length": 45
     * +"SrcHash": 72341212203333969
     * +"Type": 1
     * }
     * 16 => {#259
     * +"Offset": 1452
     * +"Length": 27
     * +"SrcHash": 72341212203333969
     * +"Type": 1
     * }
     * 17 => {#260
     * +"Offset": 1489
     * +"Length": 170
     * +"SrcHash": 72341212203333969
     * +"Type": 1
     * }
     * ]
     * }
     * }
     * }
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function text()
    {
        return $this->data->GetReportViewResult->Details->Text;
    }
    public function linkForEditors()
    {
	return 'https://' . config('antiplagiat.company_name') . '.antiplagiat.ru' . $this->data->GetReportViewResult->Summary->ReportWebId;
    }
    public function link()
    {
	return 'http://' . config('antiplagiat.company_name') . '.antiplagiat.ru' . $this->data->GetReportViewResult->Summary->ReadonlyReportWebId;
    }

    public function score()
    {
        return $this->data->GetReportViewResult->Summary->Score;
    }

    /**
     * @return AntiplagiatApiService[]
     */
    public function services()
    {
        $services = collect();
        foreach ($this->data->GetReportViewResult->CheckServiceResults as $service) {
            $services->push(new AntiplagiatApiService($service));
        }
        return $services;
    }

    /**
     * @return AntiplagiatApiBlock[]
     */
    public function blocks()
    {
        $blocks = collect();
        if (isset($this->data->GetReportViewResult->Details->CiteBlocks)) {
            foreach($this->data->GetReportViewResult->Details->CiteBlocks as $block) {
             $blocks->push(new AntiplagiatApiBlock($block));
            }
        }
        return $blocks;
    }

}