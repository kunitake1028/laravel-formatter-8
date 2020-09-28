<?php namespace SoapBox\Formatter\Parsers;

class XmlParser extends Parser
{
    private $xml;

    private function objectify($value)
    {
        $temp = $value;
        if (is_string($value)) {
            $value = preg_replace('#&(?=[a-z_0-9]+=)#', '&amp;', $value);
            $value = preg_replace('/<COVER (.*?)>/', '<COVER $1 />', $value);
            $value = preg_replace('/<XUI (.*?)>/', '<COVER $1 />', $value);
            $value = preg_replace('/<TGP (.*?)>/', '<COVER $1 />', $value);
            $temp = simplexml_load_string($value, 'SimpleXMLElement', LIBXML_NOCDATA);
        }

        $result = [];

        foreach ((array) $temp as $key => $value) {
            if ($key === "@attributes") {
                $result['_' . key($value)] = $value[key($value)];
            } elseif (is_array($value) && count($value) < 1) {
                $result[$key] = '';
            } else {
                $result[$key] = (is_array($value) or is_object($value)) ? $this->objectify($value) : $value;
            }
        }

        return $result;
    }

    public function __construct($data)
    {
        $this->xml = $this->objectify($data);
    }

    public function toArray()
    {
        return (array) $this->xml;
    }
}
