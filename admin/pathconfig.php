<?php
// $Id: config.php,v 1.4 Date: 06/01/2003, Author: Catzwolf Exp $
//
// Options setting
// Only for users who have admin right to system

require __DIR__ . '/admin_header.php';
require_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';
require_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/functions.php';

$xoopsModule = XoopsModule::getByDirname('zmagazine');
$myts = MyTextSanitizer::getInstance();

$result = $xoopsDB->queryF('SELECT articlesapage,  filesbasepath,  graphicspath,  sgraphicspath,  smiliepath, htmlpath FROM ' . $xoopsDB->prefix('zmag_config') . ' ');
[$articlesapage, $filesbasepath, $graphicspath, $sgraphicspath, $smiliepath, $htmlpath] = $xoopsDB->fetchRow($result);

global $xoopsConfig, $xoopstheme, $xoopsDB, $_POST, $myts, $xoopsUser, $wfsConfig;

if (!$xoopsUser->isAdmin($xoopsModule->mid())) {
    redirect_header('index.php', 1, _NOPERM);

    exit();
}

$op = '';
$userpath = '';

if (isset($_GET['op'])) {
    $op = $_GET['op'];
}
if (isset($_POST['op'])) {
    $op = $_POST['op'];
}

if (1 != $xoopsUser->uid() && '1' == $wfsConfig['webmstonly']) {
    redirect_header('index.php', 2, _AM_NOADMINRIGHTS);

    exit();
}

switch ($op) {
case 'save':

        if (0 == $_POST['defaults']) {
            global $xoopsConfig, $xoopsDB, $_POST, $myts;

            if ('' == $_POST['filesbasepath']) {
                $filesbasepath = $myts->addSlashes('modules/' . $xoopsModule->dirname() . '/cache/uploaded');
            } else {
                $filesbasepath = $myts->addSlashes($_POST['filesbasepath']);
            }

            if ('' == $_POST['graphicspath']) {
                $graphicspath = $myts->addSlashes('modules/' . $xoopsModule->dirname() . '/images/article');
            } else {
                $graphicspath = $myts->addSlashes($_POST['graphicspath']);
            }

            if ('' == $_POST['sgraphicspath']) {
                $sgraphicspath = $myts->addSlashes('modules/' . $xoopsModule->dirname() . '/images/category');
            } else {
                $sgraphicspath = $myts->addSlashes($_POST['sgraphicspath']);
            }

            if ('' == $_POST['smiliepath']) {
                $smiliepath = $myts->addSlashes('uploads');
            } else {
                $smiliepath = $myts->addSlashes($_POST['smiliepath']);
            }

            if ('' == $_POST['htmlpath']) {
                $htmlpath = $myts->addSlashes('modules/' . $xoopsModule->dirname() . '/html');
            } else {
                $htmlpath = $myts->addSlashes($_POST['htmlpath']);
            }

            $xoopsDB->query('update ' . $xoopsDB->prefix('zmag_config') . " set filesbasepath='$filesbasepath', graphicspath='$graphicspath', sgraphicspath='$sgraphicspath',smiliepath='$smiliepath', htmlpath='$htmlpath' ");

            redirect_header('pathconfig.php', 1, _AM_DBUPDATED);

            exit();
        }

        if ($_POST['defaults'] = 1) {
            $xoopsDB->query('update ' . $xoopsDB->prefix('zmag_config') . " set filesbasepath ='modules/zmagazine/cache/uploaded', graphicspath ='modules/zmagazine/images/article', sgraphicspath ='modules/zmagazine/images/category', smiliepath ='uploads', htmlpath ='modules/zmagazine/html'");

            redirect_header('pathconfig.php', 1, _AM_REVERTED);

            exit();
        }
        break;
        default:

        xoops_cp_header();
        global $xoopsConfig, $xoopsDB, $_POST, $myts, $eh, $wfsConfig;

    echo '<div><h3>' . _AM_GENERALCONF . '</h3></div>';
    //Finish file/dir checks here

// start of file path config
    echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
    echo "<colspan='2'><form action='pathconfig.php' method='post'>";

    //Finish file/dir checks here

// start of file path config
    echo "<tr><td class='bg3' colspan='2' height='20'><b>" . _AM_FILEPATH . '</b></td></tr>';
    echo "<tr><td class='odd' align=left colspan='2'>" . _AM_FILEPATHWARNING . '</td></tr>';

//start of upload files
    echo "<tr><td class='head' align=left colspan='2'><b>" . _AM_FILESBASEPATH . '</b></td></tr>';
    echo "<tr><td class='even' align=left colspan='2'><b>" . _AM_FILEUSEPATH . ': </b>' . sprintf(XOOPS_URL . '/') . '';
    echo "<input type='text' name='filesbasepath' value='" . $filesbasepath . "' size='42'>";
    $getcorrect = getcorrectpath($filesbasepath);
        echo "<tr><td class='even' align=left colspan='2'>";
    echo '<b>' . $getcorrect . '</b>';
    echo '' . sprintf(XOOPS_URL . '/' . $filesbasepath) . '/';
    echo '</td></tr>';

//start of upload article graphics
    echo "<tr><td class='head' align=left colspan='2'><b>" . _AM_AGRAPHICPATH . '</b></td></tr>';
    echo "<tr><td class='even' align=left colspan='2'><b>" . _AM_FILEUSEPATH . ': </b>' . sprintf(XOOPS_URL . '/') . '';
    echo "<input type='text' name='graphicspath' value='" . $graphicspath . "' size='42'>";
    $getcorrect = getcorrectpath($graphicspath);
    echo '</td></tr>';
    echo "<tr><td class='even' align=left colspan='2'>";
    echo '<b>' . $getcorrect . '</b>'; echo '' . sprintf(XOOPS_URL . '/' . $graphicspath) . '/';
    echo '</td></tr>';

    echo "<tr><td class='head' align=left colspan='2'><b>" . _AM_SGRAPHICPATH . '</b></td></tr>';
    echo "<tr><td class='even' align=left colspan='2'><b>" . _AM_FILEUSEPATH . ': </b>' . sprintf(XOOPS_URL . '/') . '';
    echo "<input type='text' name='sgraphicspath' value='" . $sgraphicspath . "' size='42'>";
    $getcorrect = getcorrectpath($sgraphicspath);
    echo '</td></tr>';
    echo "<tr><td class='even' align=left colspan='2'>";
    echo '<b>' . $getcorrect . '</b>'; echo '' . sprintf(XOOPS_URL . '/' . $sgraphicspath) . '/';
    echo '</td></tr>';

    echo "<tr><td class='head' align=left colspan='2'><b>" . _AM_SMILIECPATH . '</b></td></tr>';
    echo "<tr><td class='even' align=left colspan='2'><b>" . _AM_FILEUSEPATH . ': </b>' . sprintf(XOOPS_URL . '/') . '';
    echo "<input type='text' name='smiliepath' value='" . $smiliepath . "' size='42'>";
    $getcorrect = getcorrectpath($smiliepath);
    echo '</td></tr>';
    echo "<tr><td class='even' align=left colspan='2'>";
    echo '<b>' . $getcorrect . '</b>'; echo '' . sprintf(XOOPS_URL . '/' . $smiliepath) . '/';
    echo '</td></tr>';

    echo "<tr><td class='head' align=left colspan='2'><b>" . _AM_HTMLCPATH . '</b></td></tr>';
    echo "<tr><td class='even' align=left colspan='2'><b>" . _AM_FILEUSEPATH . ': </b>' . sprintf(XOOPS_URL . '/') . '';
    echo "<input type='text' name='htmlpath' value='" . $htmlpath . "' size='42'>";
    $getcorrect = getcorrectpath($htmlpath);
    echo '</td></tr>';
    echo "<tr><td class='even' align=left colspan='2'>";
    echo '<b>' . $getcorrect . '</b>'; echo '' . sprintf(XOOPS_URL . '/' . $htmlpath) . '/';
    echo '</td></tr>';

    echo "<tr><td class='odd' colspan='2'>&nbsp;</td></tr>";
    echo "<tr><td class='bg3' colspan='2' height='20'><b>" . _AM_CMODHEADER . '</b></td></tr>';
    echo "<tr><td class='odd' colspan='2'>" . _AM_CMODERRORINFO . '</td></tr>';

    @chmod(XOOPS_ROOT_PATH . '/' . $filesbasepath, 0777);
    echo "<tr><td class='even' width=60%>";
    echo '<b>' . _AM_FILEUPLOADSPATH . '</b>' . sprintf(XOOPS_ROOT_PATH . '/' . $filesbasepath) . '';
    echo "</td><td  class='odd'>&nbsp;<b>Attr:</b> " . get_perms(XOOPS_ROOT_PATH . '/' . $filesbasepath);
    if (!is_dir(XOOPS_ROOT_PATH . '/' . $filesbasepath) or !is_writable(XOOPS_ROOT_PATH . '/' . $filesbasepath)) {
        echo '<i><b><font color="#FF0000">' . _AM_CMODERROR . '</font></b></i>';
    }
    echo '</td><td>';
    if ($filesbasepath) {
        $filesbasepath .= '/' . 'temp';
    } else {
        $filesbasepath .= temp;
    }

    @chmod(XOOPS_ROOT_PATH . '/' . $filesbasepath, 0777);
    echo "<tr><td class='even'>";
    echo '<b>' . _AM_FILEUPLOADSTEMPPATH . '</b>' . sprintf(XOOPS_ROOT_PATH . '/' . $filesbasepath) . '';
    echo "</td><td  class='odd'>&nbsp;<b>Attr:</b> " . get_perms(XOOPS_ROOT_PATH . '/' . $filesbasepath);
    if (!is_dir(XOOPS_ROOT_PATH . '/' . $filesbasepath) or !is_writable(XOOPS_ROOT_PATH . '/' . $filesbasepath)) {
        echo ' <i><b><font color="#FF0000">' . _AM_CMODERROR . '</font></b></i>';
    }
    echo '</td><td>';

    str_replace('//', '/', $graphicspath);
    @chmod(XOOPS_ROOT_PATH . '/' . $graphicspath, 0777);
    echo "<tr><td class='even' width=60%>";
    echo '<b>' . _AM_ARTICLESFILEPATH . '</b>' . sprintf(XOOPS_ROOT_PATH . '/' . $graphicspath) . '';
    echo "</td><td  class='odd'>&nbsp;<b>Attr:</b> " . get_perms(XOOPS_ROOT_PATH . '/' . $graphicspath);
    if (!is_dir(XOOPS_ROOT_PATH . '/' . $graphicspath) or !is_writable(XOOPS_ROOT_PATH . '/' . $graphicspath)) {
        echo '<i><b><font color="#FF0000">' . _AM_CMODERROR . '</font></b></i>';
    }
    echo '</td><td>';

    str_replace('//', '/', $sgraphicspath);
    chmod(XOOPS_ROOT_PATH . '/' . $sgraphicspath, 0777);
    echo "<tr><td class='even' width=60%>";
    echo '<b>' . _AM_SECTIONFILEPATH . '</b>' . sprintf(XOOPS_ROOT_PATH . '/' . $sgraphicspath) . '';
    echo "</td><td  class='odd'>&nbsp;<b>Attr:</b> " . get_perms(XOOPS_ROOT_PATH . '/' . $sgraphicspath);
    if (!is_dir(XOOPS_ROOT_PATH . '/' . $sgraphicspath) or !is_writable(XOOPS_ROOT_PATH . '/' . $sgraphicspath)) {
        echo '<i><b><font color="#FF0000">' . _AM_CMODERROR . '</font></b></i>';
    }
    echo '</td><td>';

    str_replace('//', '/', $smiliepath);
    @chmod(XOOPS_ROOT_PATH . '/' . $smiliepath, 0777);
    echo "<tr><td class='even' width=60%>";
    echo '<b>' . _AM_SMILIEFILEPATH . '</b>' . sprintf(XOOPS_ROOT_PATH . '/' . $smiliepath) . '';
    echo "</td><td  class='odd'>&nbsp;<b>Attr:</b> " . get_perms(XOOPS_ROOT_PATH . '/' . $smiliepath);
    if (!is_dir(XOOPS_ROOT_PATH . '/' . $smiliepath) || !is_writable(XOOPS_ROOT_PATH . '/' . $smiliepath)) {
        echo '<i><b><font color="#FF0000">' . _AM_CMODERROR . '</font></b></i>';
    }
    echo '</td><td>';

    str_replace('//', '/', $htmlpath);
    chmod(XOOPS_ROOT_PATH . '/' . $htmlpath, 0777);
    echo "<tr><td class='even' width=60%>";
    echo '<b>' . _AM_HTMLFILEPATH . '</b>' . sprintf(XOOPS_ROOT_PATH . '/' . $htmlpath) . '';
    echo "</td><td  class='odd'>&nbsp;<b>Attr:</b> " . get_perms(XOOPS_ROOT_PATH . '/' . $htmlpath);
    if (!is_dir(XOOPS_ROOT_PATH . '/' . $htmlpath) || !is_writable(XOOPS_ROOT_PATH . '/' . $htmlpath)) {
        echo '<i><b><font color="#FF0000">' . _AM_CMODERROR . '</font></b></i>';
    }
    echo '</td><td>';

    $defaults = '0';
    if (1 == $xoopsUser->uid()) {
        if ($xoopsUser->isadmin($xoopsModule->mid())) {
            echo "<tr><td class='odd' colspan='2'>&nbsp;</td></tr>";

            echo "<tr><td class='head'>" . _AM_DEFAULTS . '</td>';

            echo "<td class='even'>";

            if ('1' == $defaults) {
                echo "<input type='radio' name='defaults' value='1' checked>&nbsp;" . _AM_YES . '&nbsp;';

                echo "<input type='radio' name='defaults' value='0'>&nbsp;" . _AM_NO . '&nbsp;';
            } else {
                echo "<input type='radio' name='defaults' value='1'>&nbsp;" . _AM_YES . '&nbsp;';

                echo "<input type='radio' name='defaults' value='0' checked>&nbsp;" . _AM_NO . '&nbsp;';
            }
        }
    }
    $defaults = $defaults;

    echo "<tr><td class='odd' colspan='2' align='center'><br><input type='hidden' name='op' value='save'>";
    echo "<input type='submit' value='" . _AM_SAVECHANGE . "'>";
    echo "&nbsp;<input type='button' value='" . _AM_CANCEL . "' onclick='javascript:history.go(-1)'>";
    echo '</form>';
    echo'</td></tr></table>';

    break;
}

    clearstatcache();
xoops_cp_footer();
