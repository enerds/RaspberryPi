<?php
// PhpMusicPlayer+
// File browser

// Sort a multi dimensional array
function csort($array, $column)
{
    $i=0;
    for($i=0; $i<count($array); $i++)
    {
        $sortarr[]=$array[$i][$column];
    }

    array_multisort($sortarr, $array);

    return($array);
}

include_once("functions.inc.php");

if(array_key_exists('add_dir',$_POST) || array_key_exists('add',$_POST)) {
    if(isset($_POST['add_dir']) && count($_POST['add_dir']) > 0) $mpd->PLAddBulk($_POST['add_dir']);
    if(isset($_POST['add']) && count($_POST['add']) > 0) $mpd->PLAddBulk($_POST['add']);
    header("location: files.php?reload=yes&dir=".$_POST['srcdir']);
}
if(array_key_exists('load',$_GET)) {
    $mpd->PLLoad($_GET['load']);
    header("location: files.php?reload=yes");
}
if(array_key_exists('remove',$_GET)) {
    $mpd->Remove($_GET['remove']);
    header("location: files.php?reload=yes");
}

array_key_exists("dir",$_GET) ? $dir = $_GET['dir'] : $dir = "";

header("Content-Type: text/html; charset=UTF-8");
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$userConfig['lang'].'" lang="'.$userConfig['lang'].'">
<head>
    <meta http-equiv="Expires" content="Thu, 01 Dec 1994 16:00:00 GMT" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="styles/'.$userConfig['style'].'/browser.css" />
    <title>'._lang("Music browser").'</title>
    <script type="text/javascript">
    <!--
    function InvertSelection()
    {
        Length = document.forms[0].elements.length;

        for (i=0; i < Length; i++)
        {
            if(document.forms[0].elements[i].checked == 1)
            {
                document.forms[0].elements[i].checked = 0;
            }
            else
            {
                document.forms[0].elements[i].checked = 1;
            }
        }
    }
    function ReloadMainWindow() {
        window.opener.location.reload();
    }';

if(array_key_exists('reload',$_GET)) echo '
    window.setTimeout("ReloadMainWindow()",500);';
echo '
    -->
    </script>
</head>
<body>
<h1>'._lang("Music browser").'</h1>
<div id="location">
    <a href="files.php" id="home">'._lang("Music").'</a> / ';

if(!empty($dir)) {
    $dirs = explode("/",$dir);
    $path = "";
    foreach($dirs as $el) {
        $path.= $el;
        echo '<a href="files.php?dir='.$path.'">'.stripslashes($el).'</a> / ';
        $path.= '/';
    }
}
echo '</div>
<form method="post" action="files.php">
<input type="hidden" name="srcdir" value="'.$dir.'" />
<div id="actions">
    <input type="submit" value="'._lang("Add checked items to playlist").'" />
    <input type="button" value="'._lang("Invert selection").'" onclick="InvertSelection();" />
</div>';

$list = $mpd->getDir($dir);


array_key_exists("directories",$list)
    ? $dirList = $list['directories']
    : $dirList = array();

if(count($dirList) > 0)
{
    echo '
<div id="directories">
    <h3>'._lang("Directories").'</h3>
    <ul>';

    natcasesort($dirList);
    reset($dirList);
    foreach($dirList as $el)
    {
        $pos = strrpos($el,"/");
        $title = substr($el,(($pos > 0) ? ($pos+1) : 0));
        $title = str_replace("_"," ",$title);
        echo '
        <li>
          <input type="checkbox" name="add_dir[]" value="'.$el.'" />
          <a href="files.php?dir='.urlencode($el).'">'.$title.'</a>
        </li>';
    }
    echo '
    </ul>
</div>';
}

array_key_exists("playlists",$list)
    ? $plList = $list['playlists']
    : $plList = array();

if(count($plList) > 0)
{
    echo '
<div id="playlists">
    <h3>'._lang("Playlists").'</h3>
    <ul>';


    foreach($plList as $el)
    {
        $pos = strrpos($el,"/");
        $title = substr($el,(($pos > 0) ? ($pos+1) : 0));
        echo '
        <li>
          <a href="files.php?load='.urlencode($el).'" class="load">'.$title.'</a>
          <a href="files.php?remove='.urlencode($el).'" class="remove">'._lang("Remove").'</a>
        </li>';
    }
    echo '
    </ul>
</div>';
}

array_key_exists("files",$list)
    ? $fileList = $list['files']
    : $fileList = array();

if(count($fileList) > 0)
{
    echo '
<div id="files">
    <h3>'._lang("Files").'</h3>
    <ul>';

//    natcasesort($fileList);
    reset($fileList);

    foreach($fileList as $element)
    {
        //$info = $list["info"][$id];
        //$info['file'] = $el;
        //$title = GetSongTitle($info);
        $file = $element['file'];
        $title = $element['Title'];
        echo '
        <li>
          <input type="checkbox" name="add[]" value="'.$file.'" id="file'.$file.'" />
          <label for="file'.$file.'">'.$title.'</label>
        </li>';
    }
    echo '
    </ul>
</div>';
}

echo '</form>
<form method="post" action="files.php?dir='.$dir.'" id="search_form">
    <h3><label for="f_search">'._lang("Search").'</label></h3>
    <p>
      <input type="text" name="search" size="20" id="f_search" value="'.
      (array_key_exists("search",$_POST) ? $_POST['search'] : '').'" />
      <select name="search_type">
        <option value="'.MPD_SEARCH_ARTIST.'">'._lang("Artist").'</option>
        <option value="'.MPD_SEARCH_ALBUM.'">'._lang("Album").'</option>
        <option value="'.MPD_SEARCH_TITLE.'">'._lang("Title").'</option>
        <option value="'.MPD_SEARCH_FILENAME.'">'._lang("Filename").'</option>
      </select>
      <input type="submit" value="'._lang("OK").'" />
    </p>
</form>';

if(array_key_exists('search',$_POST))
{
    echo '
<div id="search_results">
    <a name="results" class="hidden">#</a>
    <h3>'._lang("Search results").'</h3>';
    $results = $mpd->Search($_POST['search_type'],$_POST['search']);
    $results = $results['files'];
    if(count($results) < 1) echo '<p>'._lang("No results found to your search.").'</p>';
    else
    {
        echo '<ul>';
        foreach($results as $el)
        {
            if($_POST['search_type'] == MPD_SEARCH_FILENAME) {
                $title = $el["file"];
            }
            elseif(empty($el["Title"])) {
                $pos = strrpos($el["file"],"/");
                $title = substr($el["file"],(($pos > 0) ? ($pos+1) : 0));
            }
            else $title = (!empty($el["Artist"]) ? $el["Artist"]." - " : "").$el["Title"];

            $pos = strrpos($el["file"],"/");
            $path = substr($el["file"],0,$pos);
            echo '<li><a href="files.php?dir='.$path.'">'.$title.'</a></li>';
        }
        echo '</ul>';
    }
    echo '
</div>';
}

echo '
</body>
</html>';

?>