<?php



$xml = simplexml_load_file(__DIR__.'/../cal.xml');

echo 'Old Version: '.$xml->version."\r\n";
echo 'New version: ';
$version = stream_get_line(STDIN, 1024, PHP_EOL);

$xml->version = $version;
$xml->creationDate = date('Y-m-d');

$out = new SimpleXMLElement('<build></build>');
$out->addChild('version', $xml->version);
$out->addChild('date', $xml->creationDate);
$out->addChild('timestamp', time());
$out->addChild('gitshort', exec('git log -1 --pretty=format:%h'));
$out->addChild('git', exec('git log -1 --pretty=format:%H'));


echo "Saving build.xml\r\n";
$out->asXML(__DIR__.'/../admin/build.xml');

echo "Saving cal.xml\r\n";
$cal = new DOMDocument('1.0');
$cal->preserveWhiteSpace = false;
$cal->formatOutput = true;
$cal->loadXML($xml->asXML());
$cal->save(__DIR__.'/../cal.xml');

echo "Git reset\r\n";
echo exec('git reset');
echo "Git add\r\n";
echo exec('git add '.__DIR__.'/../cal.xml');
echo exec('git add '.__DIR__.'/../admin/build.xml');
echo "Git commit\r\n";
echo exec('git commit -m "Version '.$version.' deployed"');
