<?php
require __DIR__ . '/admin_header.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/mimetype.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/common.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/wfsarticle.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/wfscategory.php';

$op = '';

global $wfsConfig;

foreach ($_POST as $k => $v) {
    ${$k} = $v;
}

foreach ($_GET as $k => $v) {
    ${$k} = $v;
}

if (isset($_GET['op'])) {
    $op = $_GET['op'];
}
if (isset($_POST['op'])) {
    $op = $_POST['op'];
}

switch ($op) {
default:

global $xoopsDB, $xoopsConfig, $xoopsModule, $wfsConfig, $_GET;

xoops_cp_header();
    echo '<div><h4>' . _AM_ATTACHEDFILEM . '</h4></div>';
    adminmenu();

    echo '<div><h4>' . _AM_ATTACHEDFILE . '</h4></div>';
    echo "<table border='0' cellpadding='2' cellspacing='1' width = '100%' class = 'outer'>";

        echo "<tr class='bg3'><td align='center'>" . _AM_FILEID . "</td>
		<td align='center' class='nw'>" . _WFS_ARTICLE . "</td>
		<td align='center'>" . _AM_FILESTORE . "</td>
		<td align='center'>" . _AM_REALFILENAME . "</td>
		
		<td align='center'>" . _AM_FILEICON . "</td>
		<td align='center' class='nw'>" . _AM_FILEMIMETYPE . "</td>
		<td align='center'>" . _AM_FILESIZE . "</td>
		
		
		<td align='center'>" . _AM_ACTION . '</td></tr>';
        echo '</tr>';

        $category = new WfsCategory();
        $result = $xoopsDB->query('SELECT fileid, filerealname, filetext ,articleid, fileshowname, date, ext, minetype, downloadname, counter, filedescript FROM ' . $xoopsDB->prefix('wfs_files') . ' ');
        $totalfiles = $xoopsDB->getRowsNum($result);
        $mimetype = new mimetype();

        if ('0' == $totalfiles) {
            echo '<tr >';

            echo "<td class='head' align = 'center' colspan = '8'>No Files Found</td>";

            echo '</tr>';
        }
        echo 'Total Attached file/s: ' . $totalfiles . '<br><br>';

        while (list($fileid, $filerealname, $filetext, $articleid, $fileshowname, $date, $ext, $minetype, $downloadname, $counter, $filedescript) = $xoopsDB->fetchRow($result)) {
            $icon = get_icon(XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath'] . '/' . $filerealname);

            $iconshow = '<img src=' . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/images/icon/' . $icon . ' align = absmiddle>';

            $mimeshow = $mimetype->getType(XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath'] . '/' . $filerealname);

            $editlink = "<a href='index.php?op=fileedit&amp;fileid=" . $fileid . "'>" . _AM_EDIT . '</a>';

            $dellink = "<a href='index.php?op=delfile&amp;fileid=" . $fileid . "'>" . _AM_DELETE . '</a>';

            if (!is_file(XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath'] . '/' . $filerealname)) {
                $filerealname = 'Error!';
            }

            if (is_file(XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath'] . '/' . $filerealname)) {
                $size = Prettysize(filesize(XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath'] . '/' . $filerealname));
            } else {
                $size = '0';
            }

            $article = new WfsArticle($articleid);

            $articlelink = "<a href='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/article.php?articleid=' . $articleid . "'>" . $article->textLink() . '</a>';

            echo '<tr>';

            echo "<td class='head' align = 'center' >$fileid</td>";

            echo "<td align='center' class='even'>$articlelink</td>";

            echo "<td class='even' align = 'center' nowrap='nowrap'>$filerealname</a></td>";

            echo "<td class='even' align = 'center'>$fileshowname</td>";

            echo "<td class='even' align = 'center' nowrap='nowrap' >$iconshow ($ext)</a></td>";

            echo "<td align='center' class='even'>$mimeshow</td>";

            echo "<td align='center' class='even'>$size</td>";

            echo "<td align='center' class='even'>$editlink $dellink</td>";

            echo '</tr>';
        }
    echo '</table>';

wfsfooter();
xoops_cp_footer();
}
