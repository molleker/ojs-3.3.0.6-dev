<?php


namespace App\Components;


use App\LocaleFile;
use app\models\LocaleContent;
use PhpParser\Serializer\XML;
use SimpleXMLElement;

class OJSLangs
{

    private static function getFullFileName($file_name)
    {
        return base_path() . '/../www/' . $file_name . '.xml';
    }

    private static function getXml($file_name)
    {
        return new SimpleXMLElement(file_get_contents(self::getFullFileName($file_name)));
    }

    private static function removeChild(SimpleXMLElement $xml, $alias)
    {
        foreach ($xml->message as $key => $message) {
            foreach ($message->attributes() as $attribute => $value) {
                if ($attribute === 'key' && $value == $alias) {
                    $dom = dom_import_simplexml($message);
                    $dom->parentNode->removeChild($dom);
                    break 2;
                }
            };
        };
    }

    private static function setEntry(SimpleXMLElement $xml, $alias, $set_value)
    {
        self::removeChild($xml, $alias);
        $message = $xml->addChild('message', $set_value);
        $message->addAttribute('key', $alias);
        return $message;
    }

    private static function getLocaleFileContent($file_name, $alias)
    {
        return LocaleFile::where('name', '/' . $file_name . '.xml')->first()->content()->firstOrNew(['key' => $alias]);
    }

    public static function setLang($file_name, $alias, $value)
    {
        $xml = self::getXml($file_name);
        self::setEntry($xml, $alias, $value);
        $xml->saveXML(self::getFullFileName($file_name));
        $content = self::getLocaleFileContent($file_name, $alias);
        $content->content = $value;
        $content->save();
    }

    public static function removeLang($file_name, $alias)
    {
        $xml = self::getXml($file_name);
        self::removeChild($xml, $alias);
        $xml->saveXML(self::getFullFileName($file_name));
        self::getLocaleFileContent($file_name, $alias)->delete();
    }
}