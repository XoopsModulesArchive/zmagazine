<?php

global $xoopsTheme, $xoopsModule, $xoopsUser, $xoopsDB;

$result = $xoopsDB->queryF('SELECT * FROM ' . $xoopsDB->prefix('zmag_config') . ' ');
[
    $articlesapage,
    $filesbasepath,
    $graphicspath,
    $sgraphicspath,
    $smiliepath,
    $htmlpath,
    $toppagetype,
    $wysiwygeditor,
    $showcatpic,
    $comments,
    $blockscroll,
    $blockheight,
    $blockamount,
    $blockdelay,
    $submenus,
    $webmstonly,
    $lastart,
    $numuploads,
    $timestamp,
    $autoapprove,
    $showauthor,
    $showcomments,
    $showfile,
    $showrated,
    $showvotes,
    $showupdated,
    $showhits,
    $showMarticles,
    $showMupdated,
    $anonpost,
    $notifysubmit,
    $shortart,
    $shortcat,
    $novote,
    $realname,
    $indexheading,
    $indexheader,
    $indexfooter,
    $groupid,
    $indeximage,
    $noicons,
    $summary,
    $aidxpathtype,
    $aidxorder,
    $selmimetype,
    $wfsmode,
    $imgwidth,
    $imgheight,
    $filesize,
    $picon,
    $showSpacer,
    $showArchive,
    $showSpotlight
] = $xoopsDB->fetchRow($result);

$wfsConfig = [ 'articlesapage' => $articlesapage,
'filesbasepath' => $filesbasepath,
'graphicspath' => $graphicspath,
'sgraphicspath' => $sgraphicspath,
'smiliepath' => $smiliepath,
'htmlpath' => $htmlpath,
'toppagetype' => $toppagetype,
'wysiwygeditor' => $wysiwygeditor,
'showcatpic' => $showcatpic,
'comments' => $comments,
'blockscroll' => $blockscroll,
'blockheight' => $blockheight,
'blockamount' => $blockamount,
'blockdelay' => $blockdelay,
'submenus' => $submenus,
'webmstonly' => $webmstonly,
'lastart' => $lastart,
'numuploads' => $numuploads,
'timestamp' => $timestamp,
'autoapprove' => $autoapprove,
'showauthor' => $showauthor,
'showcomments' => $showcomments,
'showfile' => $showfile,
'showrated' => $showrated,
'showvotes' => $showvotes,
'showupdated' => $showupdated,
'showhits' => $showhits,
'showMarticles' => $showMarticles,
'showMupdated' => $showMupdated,
'anonpost' => $anonpost,
'notifysubmit' => $notifysubmit,
'shortart' => $shortart,
'shortcat' => $shortcat,
'novote' => $novote,
'realname' => $realname,
'indexheading' => $indexheading,
'indexheader' => $indexheader,
'indexfooter' => $indexfooter,
'groupid' => $groupid,
'indeximage' => $indeximage,
'noicons' => $noicons,
'summary' => $summary,
'aidxpathtype' => $aidxpathtype,
'aidxorder' => $aidxorder,
'selmimetype' => $selmimetype,
'wfsmode' => $wfsmode,
'imgwidth' => $imgwidth,
'imgheight' => $imgheight,
'filesize' => $filesize,
'picon' => $picon,
'showSpacer' => $showSpacer,
'showArchive' => $showArchive,
'showSpotlight' => $showSpotlight,
];

$IconArray = [
     'css.gif' => 'css',
     //"ico.gif"		  => "ico",
     'doc.gif' => 'doc',
     'html.gif' => 'html htm shtml htm',
     'pdf.gif' => 'pdf',
     'txt.gif' => 'conf sh shar csh ksh tcl cgi',
     'php.gif' => 'php php4 php3 phtml phps',
     'js.gif' => 'js',
     'sql.gif' => 'sql',
     'pl.gif' => 'pl',
     'gif.gif' => 'gif',
     'png.gif' => 'png',
     'bmp.gif' => 'bmp',
     'jpg.gif' => 'jpeg jpe jpg',
     'c.gif' => 'c cpp',
     'rar.gif' => 'rar',
     'zip.gif' => 'zip tar gz tgz z ace arj cab bz2',
     'mid.gif' => 'mid kar',
     'wav.gif' => 'wav',
     'wax.gif' => 'wax',
     'xm.gif' => 'xm',
     'ram.gif' => 'ram',
     'mpg.gif' => 'mp1 mp2 mp3 wma',
     'mp3.gif' => 'mpeg mpg mov avi rm',
     'exe.gif' => 'exe com dll bin dat rpm deb',
     'txt.gif' => 'txt ini xml xsl ini inf cfg log nfo ico',
];

$WfsHelperDir['application/pdf'] = 'xpdf-win32';
$WfsHelperDir['application/vnd.ms-excel'] = 'xlhtml-win32';

# Files that can be edited in PHPFM's text editor
$EditableFiles = 'php php4 php3 phtml phps conf sh shar csh ksh tcl cgi pl js txt ini html htm css xml xsl ini inf cfg log nfo bat';

# Files that can be viewed in PHPFM's image viewer.
$ViewableFiles = 'html htm jpeg jpe jpg gif png bmp pdf';
