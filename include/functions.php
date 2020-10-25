<?php
require_once XOOPS_ROOT_PATH . '/modules/zmagazine/class/mimetype.php';
require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';

function indexmainheader($mainlink = 1)
{
    $xoopsModule = XoopsModule::getByDirname('zmagazine');

    global $xoopsModule, $xoopsConfig, $wfsConfig;

    echo '<p><div align="center">';

    echo '<a href="' . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/index.php"><img src="' . XOOPS_URL . '/modules/zmagazine/images' . '/' . $wfsConfig['indeximage'] . '" border="0" alt""></a>';

    echo '</p>';
}

function newdownloadgraphic($time, $status)
{
    $xoopsModule = XoopsModule::getByDirname('zmagazine');

    global $wfsConfig;

    $count = 7;

    $startdate = (time() - (86400 * $count));

    if ($startdate < $time) {
        if ($wfsConfig['noicons']) {
            if (1 == $status) {
                echo '&nbsp;<img src="' . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/images/noicon/newred.gif">';
            } elseif (2 == $status) {
                echo '&nbsp;<img src="' . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/images/noicon/update.gif" >';
            }
        }
    }
}

function popgraphic($counter)
{
    $xoopsModule = XoopsModule::getByDirname('zmagazine');

    global $mydownloads_popular;

    if ($counter >= 50) {
        echo '&nbsp;<img src ="' . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/images/noicon/pop.gif" alt="' . _MD_POPULAR . '">';
    }
}
//Reusable Link Sorting Functions
function convertorderbyin($orderby)
{
    if ('articleidA' == $orderby) {
        $orderby = 'articleid ASC';
    }

    if ('titleA' == $orderby) {
        $orderby = 'title ASC';
    }

    if ('createdA' == $orderby) {
        $orderby = 'created ASC';
    }

    if ('counterA' == $orderby) {
        $orderby = 'counter ASC';
    }

    if ('ratingA' == $orderby) {
        $orderby = 'rating ASC';
    }

    if ('submitA' == $orderby) {
        $orderby = 'published ASC';
    }

    if ('articleidD' == $orderby) {
        $orderby = 'articleid DESC';
    }

    if ('titleD' == $orderby) {
        $orderby = 'title DESC';
    }

    if ('createdD' == $orderby) {
        $orderby = 'created DESC';
    }

    if ('counterD' == $orderby) {
        $orderby = 'counter DESC';
    }

    if ('ratingD' == $orderby) {
        $orderby = 'rating DESC';
    }

    if ('submitD' == $orderby) {
        $orderby = 'published DESC';
    }

    if ('weight' == $orderby) {
        $orderby = 'weight';
    }

    return $orderby;
}
function convertorderbytrans($orderby)
{
    if ('articleid ASC' == $orderby) {
        $orderbyTrans = _WFS_ARTICLEIDLTOM;
    }

    if ('articleid DESC' == $orderby) {
        $orderbyTrans = _WFS_ARTICLEIDMTOL;
    }

    if ('counter ASC' == $orderby) {
        $orderbyTrans = _WFS_POPULARITYLTOM;
    }

    if ('counter DESC' == $orderby) {
        $orderbyTrans = _WFS_POPULARITYMTOL;
    }

    if ('title ASC' == $orderby) {
        $orderbyTrans = _WFS_TITLEATOZ;
    }

    if ('title DESC' == $orderby) {
        $orderbyTrans = _WFS_TITLEZTOA;
    }

    if ('created ASC' == $orderby) {
        $orderbyTrans = _WFS_DATEOLD;
    }

    if ('created DESC' == $orderby) {
        $orderbyTrans = _WFS_DATENEW;
    }

    if ('rating ASC' == $orderby) {
        $orderbyTrans = _WFS_RATINGLTOH;
    }

    if ('rating DESC' == $orderby) {
        $orderbyTrans = _WFS_RATINGHTOL;
    }

    if ('published ASC' == $orderby) {
        $orderbyTrans = _WFS_SUBMITLTOH;
    }

    if ('published DESC' == $orderby) {
        $orderbyTrans = _WFS_SUBMITHTOL;
    }

    if ('weight' == $orderby) {
        $orderbyTrans = _WFS_WEIGHT;
    }

    return $orderbyTrans;
}
function convertorderbyout($orderby)
{
    if ('articleid ASC' == $orderby) {
        $orderby = 'articleidA';
    }

    if ('title ASC' == $orderby) {
        $orderby = 'titleA';
    }

    if ('created ASC' == $orderby) {
        $orderby = 'createdA';
    }

    if ('counter ASC' == $orderby) {
        $orderby = 'counterA';
    }

    if ('rating ASC' == $orderby) {
        $orderby = 'ratingA';
    }

    if ('published ASC' == $orderby) {
        $orderby = 'submitA';
    }

    if ('articleid DESC' == $orderby) {
        $orderby = 'articleidD';
    }

    if ('title DESC' == $orderby) {
        $orderby = 'titleD';
    }

    if ('created DESC' == $orderby) {
        $orderby = 'createdD';
    }

    if ('counter DESC' == $orderby) {
        $orderby = 'counterD';
    }

    if ('rating DESC' == $orderby) {
        $orderby = 'ratingD';
    }

    if ('published DESC' == $orderby) {
        $orderby = 'submitD';
    }

    if ('weight' == $orderby) {
        $orderby = 'weight';
    }

    return $orderby;
}

function PrettySize($size)
{
    $mb = 1024 * 1024;

    if ($size > $mb) {
        $mysize = sprintf('%01.2f', $size / $mb) . ' MB';
    } elseif ($size >= 1024) {
        $mysize = sprintf('%01.2f', $size / 1024) . ' KB';
    } else {
        $mysize = sprintf(_WFS_NUMBYTES, $size);
    }

    return $mysize;
}

//updates rating data in itemtable for a given item
function updaterating($sel_id)
{
    global $xoopsDB;

    $query = 'select rating FROM ' . $xoopsDB->prefix('zmag_votedata') . ' WHERE lid = ' . $sel_id . '';

    $voteresult = $xoopsDB->query($query);

    $votesDB = $xoopsDB->getRowsNum($voteresult);

    $totalrating = 0;

    while (list($rating) = $xoopsDB->fetchRow($voteresult)) {
        $totalrating += $rating;
    }

    $finalrating = $totalrating / $votesDB;

    $finalrating = number_format($finalrating, 4);

    $query = 'UPDATE ' . $xoopsDB->prefix('zmag_article') . " SET rating=$finalrating, votes=$votesDB WHERE articleid = $sel_id";

    $xoopsDB->query($query);
}

//returns the total number of items in items table that are accociated with a given table $table id
function getTotalItems($sel_id, $status = '')
{
    global $xoopsDB, $mytree;

    $count = 0;

    $arr = [];

    $query = 'select count(*) from ' . $xoopsDB->prefix('zmag_article') . ' where categoryid=' . $sel_id . '';

    if ('' != $status) {
        $query .= " and status>=$status";
    }

    $result = $xoopsDB->query($query);

    [$thing] = $xoopsDB->fetchRow($result);

    $count = $thing;

    $arr = $mytree->getAllChildId($sel_id);

    $size = count($arr);

    for ($i = 0; $i < $size; $i++) {
        $query2 = 'select count(*) from ' . $xoopsDB->prefix('zmag_article') . ' where categoryid=' . $arr[$i] . '';

        if ('' != $status) {
            $query2 .= " and status>=$status";
        }

        $result2 = $xoopsDB->query($query2);

        [$thing] = $xoopsDB->fetchRow($result2);

        $count += $thing;
    }

    return $count;
}

function getlast($toget)
{
    $pos = mb_strrpos($toget, '.');

    $lastext = mb_substr($toget, $pos);

    return $lastext;
}

function replace($o)
{
    $o = str_replace('/', '', $o);

    $o = str_replace('\\', '', $o);

    $o = str_replace(':', '', $o);

    $o = str_replace('*', '', $o);

    $o = str_replace('?', '', $o);

    $o = str_replace('<', '', $o);

    $o = str_replace('>', '', $o);

    $o = str_replace('"', '', $o);

    $o = str_replace('|', '', $o);

    return $o;
}

function is_valid_name($input)        ## Checks whether the directory- or filename is valid
{
    if (mb_strstr($input, '\\')) {
        return false;
    } elseif (mb_strstr($input, '/')) {
        return false;
    } elseif (mb_strstr($input, ':')) {
        return false;
    } elseif (mb_strstr($input, '?')) {
        return false;
    } elseif (mb_strstr($input, '*')) {
        return false;
    } elseif (mb_strstr($input, '"')) {
        return false;
    } elseif (mb_strstr($input, '<')) {
        return false;
    } elseif (mb_strstr($input, '>')) {
        return false;
    } elseif (mb_strstr($input, '|')) {
        return false;
    } elseif (mb_strstr($input, '£')) {
        return false;
    } elseif (mb_strstr($input, '%')) {
        return false;
    } elseif (mb_strstr($input, '^')) {
        return false;
    } elseif (mb_strstr($input, '&')) {
        return false;
    }
  

    return true;
}

function dirsize($directory)
{
    if (!is_dir($directory)) {
        return -1;
    }

    $size = 0;

    if ($DIR = opendir($directory)) {
        while (false !== ($dirfile = readdir($DIR))) {
            if (is_link($directory . '/' . $dirfile) || '.' == $dirfile || '..' == $dirfile) {
                continue;
            }

            if (is_file($directory . '/' . $dirfile)) {
                $size += filesize($directory . '/' . $dirfile);
            } elseif (is_dir($directory . '/' . $dirfile)) {
                $dirSize = format_size($directory . '/' . $dirfile);

                if ($dirSize >= 0) {
                    $size += $dirSize;
                } else {
                    return -1;
                }
            }
        }

        closedir($DIR);
    }

    return $size;
}

  function format_size($rawSize)
  {
      $kb = 1024;         // Kilobyte
          $mb = 1024 * $kb;   // Megabyte
          $gb = 1024 * $mb;   // Gigabyte
          $tb = 1024 * $gb;   // Terabyte

   if ($rawSize < $kb) {
       return $rawSize . ' bytes';
   } elseif ($rawSize < $mb) {
       return round($rawSize / $kb, 2) . ' KB';
   } elseif ($rawSize < $gb) {
       return round($rawSize / $mb, 2) . ' MB';
   } elseif ($rawSize < $tb) {
       return round($rawSize / $gb, 2) . ' GB';
   }
  

      return round($rawSize / $tb, 2) . ' TB';
  }

function myfilesize($file)
{
    if ('file' == filetype($file)) {
        $kb = 1024;         // Kilobyte
  $mb = 1024 * $kb;   // Megabyte
  $gb = 1024 * $mb;   // Gigabyte
  $tb = 1024 * $gb;   // Terabyte

   $size = filesize($file);

        if ($size < $kb) {
            return $size . ' B';
        } elseif ($size < $mb) {
            return round($size / $kb, 2) . ' KB';
        } elseif ($size < $gb) {
            return round($size / $mb, 2) . ' MB';
        } elseif ($size < $tb) {
            return round($size / $gb, 2) . ' GB';
        }
  

        return round($size / $tb, 2) . ' TB';
    }
}

function freespace($workdir)
{
    $diskfreef = disk_free_space($workdir);

    return $diskfreef;
}

function get_icon($file)        ## Get the icon from the filename
{
    global $IconArray;

    reset($IconArray);

    $extension = mb_strtolower(mb_substr(mb_strrchr($file, '.'), 1));

    if ('' == $extension) {
        return 'unknown.gif';
    }

    while (list($icon, $types) = each($IconArray)) {
        foreach (explode(' ', $types) as $type) {
            if ($extension == $type) {
                return $icon;
            }
        }
    }

    return 'unknown.gif';
}

function get_mimetype($minetype)        ## Get the icon from the filename
{
    global $wfsConfig;

    $mimetype = new mimetype();

    echo $minetype;

    foreach (explode(' ', $wfsConfig['selmimetype']) as $type) {
        echo $mimetype->privFindType($type) . '<br>';
    }

    if ($minetype === $mimetype->privFindType($type)) {
        return true;
    }

    return false;
}

function extractSubdir($oldworkdir, $backpath)
{
    $tmp = '';

    if ('' != $oldworkdir) {
        $rp = preg_replace("((.*)\/.*)\/\.\.$", '\\2', $oldworkdir);

        $tmp = strtr(str_replace($backpath, '', $rp), '\\', '/');

        while ('/' == $tmp[0]) {
            $tmp = mb_substr($tmp, 1);
        }
    }

    return $tmp;
}

function DirSelectOption($workdir, $selected, $path)
{
    global $xoopsConfig, $wfsConfig, $xoopsModule, $PHP_SELF, $workd;

    $filearray = getDirList($workdir);

    echo "<select size='1' name='workd' onchange='location.href=\"" . $path . "?rootpath=\"+this.options[this.selectedIndex].value'>";

    echo "<option value=' '>------</option>";

    foreach ($filearray as $workd) {
        if ($workd === $selected) {
            $opt_selected = 'selected';
        } else {
            $opt_selected = '';
        }

        echo "<option value='" . $workd . "' $opt_selected>" . basename($workd) . '</option>';
    }

    echo '</select>';
}

function getcorrectpath($path)
{
    if (file_exists(XOOPS_ROOT_PATH . '/' . $path)) {
        $ret = '      ' . _AM_PATHEXIST . ' ';
    } else {
        $ret = '      ' . _AM_PATHNOTEXIST . ' ';
    }

    return $ret;
}

function get_perms($file)
{
    $p_bin = mb_substr(decbin(fileperms($file)), -9);

    $p_arr = explode('.', mb_substr(chunk_preg_split($p_bin, 1, '.'), 0, 17));

    $perms = '';

    foreach ($p_arr as $i => $this) {
        $p_char = (0 == $i % 3 ? 'r' : (1 == $i % 3 ? 'w' : 'x'));

        $perms .= ('1' == $this ? $p_char : '-') . (2 == $i % 3 ? ' ' : '');
    }

    return mb_substr($perms, 0, -1);
}

function is_editable_file($filename)        ## Checks whether a file is editable
{
    global $EditableFiles;

    $extension = mb_strtolower(mb_substr(mb_strrchr($filename, '.'), 1));

    foreach (explode(' ', $EditableFiles) as $type) {
        if ($extension == $type) {
            return true;
        }
    }

    return false;
}

function is_viewable_file($filename)        ## Checks whether a file is viewable
{
    global $ViewableFiles;

    $extension = mb_strtolower(mb_substr(mb_strrchr($filename, '.'), 1));

    foreach (explode(' ', $ViewableFiles) as $type) {
        if ($extension == $type) {
            return true;
        }
    }

    return false;
}

function getuser($tuid)
{
    global $xoopsDB, $xoopsConfig;

    echo "<select name='changeuser'>";

    echo "<option value=' '>------</option>";

    $result = $xoopsDB->query('SELECT uid, uname FROM ' . $xoopsDB->prefix('users') . ' ORDER BY uname');

    while (list($uid, $uname) = $xoopsDB->fetchRow($result)) {
        if ($uid == $tuid) {
            $opt_selected = "selected='selected'";
        } else {
            $opt_selected = '';
        }

        echo "<option value='" . $uid . "' $opt_selected>" . $uname . '</option>';
    }

    echo '</select></div>';
}

function showSelectedImg($imageshow)
{
    echo "<img src='" . $imageshow . "'>";
}

function myTextForm2($url, $value)
{
    return '<form action="' . $url . '" method="post"><td width = 10% align =left><input type="submit" value="' . $value . '"></td></form>';
}

function Offlinemessage()
{
    indexmainheader();
}

function lastaccess($file, $output)
{
    global $wfsConfig;

    if (!file_exists(realpath($file))) {
        return false;
    }

    $lastvisit = filectime($file);

    $currentdate = date('U');

    $difference = $currentdate - $lastvisit;

    if ('D' == $output) {
        $fileaccess = (int)($difference / 84600);
    } elseif ('S' == $output) {
        $fileaccess = $difference;
    } elseif ('E1' == $output) {
        $fileaccess = formatTimestamp($lastvisit, $wfsConfig['timestamp']);
    } elseif ('E2' == $output) {
        $fileaccess = date('d.m Y', $lastvisit);
    }

    return $fileaccess;
}

function adminmenu()
{
    echo "<table width='100%' border='0' cellspacing='1' cellpadding = '2' class='outer'>";

    echo "<tr><td class= 'bg3'><b>" . _AM_MENU_LINKS . '</b></td></tr>';

    echo "<tr><td class = 'head'><b><a href='config.php'>" . _AM_GENERALCONF . '</a></b></td></tr>';

    echo "<tr><td class = 'even'><b><a href='category.php?op=default'>" . _AM_CATEGORYSMNGR . '</b></a></td></tr>';

    echo "<tr><td class = 'head'><a href='allarticles.php'>" . _AM_ARTICLEMANAGE . '</a></td></tr>';

    echo "<tr><td class = 'even'><b><a href='index.php?op=default'>" . _AM_PEARTICLES . '</a></b></td></tr>';

    echo "<tr><td class = 'head'><b><a href='filemanager.php'>" . _AM_UPLOADMAN . '</a></b></td></tr>';

    echo "<tr><td class = 'even'><a href='reorder.php'>" . _AM_WEIGHTMANAGE . '</a></td></tr>';

    echo "<tr><td class = 'head'><a href='wfsfilesshow.php'>" . _AM_WFSFILESHOW . '</a></td></tr>';

    echo'</table><br>';
}

function wfsfooter()
{
    echo "<br><div style='text-align:center'>" . _AM_VISITSUPPORT . '</div>';
}
