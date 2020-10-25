<?php
// WFSECTION
// Powerful Section Module for XOOPS
//
// $Id: index.php,v 1.7 Date: 06/01/2003, Author: Catzwolf Exp $
//
// Admin Main

include 'admin_header.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/wfscategory.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/wfsarticle.php';

$op = '';

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
case 'reorder':

    global $orders, $cat;
    for ($i = 0, $iMax = count($orders); $i < $iMax; $i++) {
        $xoopsDB->queryF('update ' . $xoopsDB->prefix('zmag_category') . ' set orders = ' . $orders[$i] . " WHERE id=$cat[$i]");
    }
    redirect_header('reorder.php', 1, 'Categories have been re-ordered!');

break;
case 'reaorder':

    global $weight, $art, $catid;

    for ($i = 0, $iMax = count($weight); $i < $iMax; $i++) {
        $xoopsDB->queryF('update ' . $xoopsDB->prefix('zmag_article') . ' set weight = ' . $weight[$i] . " WHERE articleid=$art[$i]");
    }
    redirect_header('reorder.php', 1, 'Articles have been re-ordered!');

break;
default:

xoops_cp_header();

global $xoopsDB, $xoopsConfig, $xoopsModule, $wfsConfig, $_GET;

$category = 0;
$start = 0;
$listtype = 0;

function listcategory($listtype = 0)
{
    global $xoopsDB, $xoopsConfig, $xoopsModule;

    $orders = [];

    $cat = [];

    echo "<form name='reorder' METHOD='post'>";

    echo "<table border='0' width='100%' cellpadding = '2' cellspacing ='1' class = 'outer'>";

    echo '<tr class = bg3>';

    echo "<td align='center' width=3% height =16 ><b>" . _AM_REORDERID . '</b>';

    echo "</td><td align='center' width=3%><b>" . _AM_REORDERPID . '</b>';

    echo "</td><td align='left' width=30%><b>" . _AM_REORDERTITLE . '</b>';

    echo "</td><td align='left'><b>" . _AM_REORDERDESCRIPT . '</b>';

    echo "</td><td align='center' width=5%><b>" . _AM_REORDERWEIGHT . '</b>';

    echo '</td></tr>';

    $xt = new WfsCategory();

    $maintopics = $xt->getFirstChild();

    $deps = 0;

    $listtype = (int)$listtype;

    showcategory($maintopics, 0, $listtype);

    echo "<tr><td class='foot' align='center' colspan='6'>
		<input type='hidden' name='op' value=reorder>
		<input type='submit' name='submit' value='" . _SUBMIT . "'>
		
		</td></tr>";

    echo '</table>';

    echo '</form>';
}

function showcategory($categorys, $deps = 0, $listtype = 0)
{
    global $xoopsConfig, $xoopsDB, $xoopsModule, $listtype, $orders, $cat, $catid;

    foreach ($categorys as $onecat) {
        $num = WfsArticle::countByCategory($onecat->id());

        $link = XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/admin/reorder.php?category=' . $onecat->id();

        $sarray = WfsArticle::getAllArticle(0, 0, $onecat->id(), $dataselect = '4');

        echo '<tr>';

        if (0 == $deps) {
            $class = 'head';
        }

        if (2 == $deps) {
            $class = 'even';
        }

        if ($deps >= 3) {
            $class = 'odd';
        }

        echo "<td align='left' class = $class>" . $onecat->id . '</td>';

        echo "<input type='hidden' name='cat[]' value='" . $onecat->id . "'>";

        echo "<td align='middle' class = $class>" . $onecat->pid . '</td>';

        echo "<td align='left' nowrap='nowrap' class = $class><a href='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/admin/reorder.php?category=' . $onecat->id() . "'>";

        echo '' . str_repeat('&nbsp;&nbsp;', $deps) . $onecat->title . '</td>';

        echo "<td align='left' class = $class>" . $onecat->description . '</td>';

        echo "<td align='left' class = $class>";

        echo "<input type='text' name='orders[]' value='" . $onecat->orders . "' size='5' maxlenght='5'></td>";

        echo '</tr>';

        //Show any sub cats if submenu === true

        $childcat = $onecat->getFirstChild();

        if ($childcat) {
            showcategory($childcat, $deps + 2, $listtype);
        }
    }
}

function listArticle($catid, $start = 0, $num = 20)
{
    global $xoopsDB, $xoopsConfig, $xoopsModule, $wfsConfig, $weight, $art;

    $xt = new WfsCategory($catid);

    $weight = [];

    $art = [];

    $sarray = WfsArticle::getAllArticle($num, $start, $catid, $dataselect = '1');

    $articlecount = WfsArticle::countByCategory($catid);

    echo "<form name='reaorder' METHOD='post'>";

    echo "<table border='0' cellpadding='2' cellspacing='1' width = '100%' class = 'outer'>";

    echo "<tr align='left'>";

    echo "<td align='center' class='bg3' width = '3%'><b>" . _AM_REORDERID . '</b></td>';

    echo "<td align='left' width = '30%'class='bg3'><b>" . _AM_REORDERTITLE . '</b></td>';

    echo "<td align='left' width = '60%' class='bg3'><b>" . _AM_REORDERSUMMARY . '</b></td>';

    echo "<td align='center' width = '17%' class='bg3'><b>" . _AM_REORDERWEIGHT . '</b></td>';

    echo '</tr>';

    if (0 != $articlecount) {
        foreach ($sarray as $article) {
            $articlelink = '';

            echo '<tr>';

            echo "<td class='head'>$article->articleid</td>";

            echo "<td class='even' nowrap='nowrap'><a href='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/admin/index.php?op=edit&articleid=' . $article->articleid() . "'>" . $article->title() . '</a></td>';

            echo "<input type='hidden' name='art[]' value='" . $article->articleid . "'>";

            echo "<td align='left'class='odd'>" . $article->summary . '</td>';

            echo "<td align='center' class='even'><input type='text' name='weight[]' value='" . $article->weight . "' size='5' maxlenght='5'></td>";

            echo '</tr>';
        }
    } else {
        echo '<tr>';

        echo "<td colspan = 4 align = 'center' class='even'>No Articles within this section</td>";

        echo '</tr>';
    }

    echo "
		<tr><td> </td></tr>
		<tr><td class='foot' align='center' colspan='4'>
		<input type='hidden' name='op' value=reaorder>
		<input type='submit' name='submit' value='" . _SUBMIT . "'>
		</td></tr>
		
		";

    echo '</table>';

    echo "<table border='0' cellpadding='1' cellspacing='1' width='100%'>";

    echo '<br>';

    echo "<tr><td align='center' class='head' >[ <a href='javascript:history.back(1)'><a href='./reorder.php'>Return to Category re-order</a> ]</a></td></tr>";

    echo '</table>';

    echo '<br>';
}

global $xoopsModule, $xoopsUser, $xoopsDB, $myts, $wfsConfig, $_GET;

if (isset($_GET['category']) && preg_match('^[0-9]{1,}$', $_GET['category'])) {
    $category = $_GET['category'];
} else {
    $category = 0;
}

if (isset($_GET['start']) && is_numeric($_GET['start'])) {
    $start = $_GET['start'];
} else {
    $start = 0;
}

$orderby = 'weight';

$listtype = 0;

if (isset($_GET['listtype'])) {
    $listtype = (int)$_GET['listtype'];
}

        echo '<div><h4>' . _AM_CAREORDER . '</h4></div>';
        adminmenu();

if (empty($category)) {
    echo '<div><h4>' . _AM_CAREORDER2 . '</h4></div>';

    echo '' . _AM_CATREORDERTEXT . '';

    listcategory($listtype);
} else {
    echo '<div><h4>' . _AM_CAREORDER3 . '</h4></div>';

    listArticle($category, $start, 10);
}
wfsfooter();
xoops_cp_footer();
}
