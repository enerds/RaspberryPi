<?php
// PHPMusicPlayer+
// Lang Generation file

function search_strings($file)
{
    $content = implode("",file("../".$file));
    preg_match_all("/_lang\([\"\']([^\"']+)[\"\']\)/Ui",$content,$strings,PREG_PATTERN_ORDER);
    $out = $strings[1];
    return $out;
}


$dir = @opendir("..");
if(!$dir)
    die("Opendir restriction in effect or other restriction: cannot open parent dir.");

echo "Searching text strings inside source files... Please wait...";
flush();

while($file = readdir($dir))
{
    if(eregi("\.php$",$file))
    {
        if(isset($strings) && is_array($strings))
            $strings = array_merge($strings,search_strings($file));
        else
            $strings = search_strings($file);
    }
}

$strings = array_unique($strings);

// for debug
#echo "<pre>";
#print_r($strings);

$out = '<?php
# -*- coding: utf-8 -*-
# (Don\'t remove this line)

// PHPMusicPlayer+
// Lang file: lang name

$lang_strings =
array(';

foreach($strings as $str)
{
    $out.= "\n  \"{$str}\"\n    => \"{$str}\",\n";
}

$out = substr($out,0,-2);
$out.= '
);
?>';

echo '<hr>OK, copy/paste the file below to a new file in the <i>lang/</i> directory and traduce using this example:<br>
<pre>  "Original string"
    => "Traduced string",</pre><hr>';
echo "<pre>";
echo htmlspecialchars($out);
echo "</pre>";

?>