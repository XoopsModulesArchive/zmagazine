<?php

require __DIR__ . '/admin_header.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/wfscategory.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/wfsarticle.php';

global $xoopsDB, $xoopsConfig, $xoopsModule, $wfsConfig, $HTTP_SERVER_VARS;

foreach ($_POST as $k => $v) {
    ${$k} = $v;
}

foreach ($_GET as $k => $v) {
    ${$k} = $v;
}

if (isset($_GET['lastarts'])) {
    $xoopsOption = (int)$_GET['lastarts'];

    if ($xoopsOption > 30) {
        $xoopsOption = 0;
    }
} else {
    $xoopsOption = 0;
}

if (isset($_GET['start'])) {
    $start = (int)$_GET['start'];
} else {
    $start = 0;
}

if (isset($_GET['orderby'])) {
    $orderby = convertorderbyin($_GET['orderby']);
} else {
    $orderby = 'articleid ASC';
}

if (!isset($action)) {
    $action = 'all';
}

if ('published' == $action) {
    $dataselect = 1;
}
if ('submitted' == $action) {
    $dataselect = 2;
}
if ('all' == $action) {
    $dataselect = 3;
}
if ('online' == $action) {
    $dataselect = 4;
}
if ('offline' == $action) {
    $dataselect = 5;
}
if ('autoexpire' == $action) {
    $dataselect = 6;
}
if ('autoart' == $action) {
    $dataselect = 7;
}
if ('expired' == $action) {
    $dataselect = 8;
}
if ('noshowart' == $action) {
    $dataselect = 9;
}
// added ladon
if ('spotlight' == $action) {
    $dataselect = 10;
}

xoops_cp_header();

        $articlearray = WfsArticle::getAllArticle($wfsConfig['lastart'], $start, $xoopsOption, $dataselect);
        $scount = count(WfsArticle::getAllArticle($wfsConfig['lastart'], 0, 0, $dataselect));
        $totalcount = count(WfsArticle::getAllArticle(0, 0, 0, $dataselect));

        echo '<div><h4>' . _AM_ARTICLEMANAGEMENT . '</h4></div>';
        adminmenu();

        echo "<table width='100%' border='0' cellpadding = '2' cellspacing='0' class='outer'>";
        echo "<tr class = 'even'><td>";
        echo "<div><a href='" . $HTTP_SERVER_VARS['PHP_SELF'] . "?action=all'>" . _AM_ALLARTICLES . '</a></div>';
        echo "<div><a href='" . $HTTP_SERVER_VARS['PHP_SELF'] . "?action=published'>" . _AM_PUBLARTICLES . '</a></div>';
        echo "<div><a href='" . $HTTP_SERVER_VARS['PHP_SELF'] . "?action=autoart'>" . _AM_AUTOARTICLES . '</a><div>';
        echo "<div><a href='" . $HTTP_SERVER_VARS['PHP_SELF'] . "?action=expired'>" . _AM_EXPIREDARTICLES . '</a><div>';
        echo "<div><a href='" . $HTTP_SERVER_VARS['PHP_SELF'] . "?action=autoexpire'>" . _AM_AUTOEXPIREARTICLES . '</a><div>';
        echo "<div><a href='" . $HTTP_SERVER_VARS['PHP_SELF'] . "?action=submitted'>" . _AM_SUBLARTICLES . '</a><div>';
        echo "<div><a href='" . $HTTP_SERVER_VARS['PHP_SELF'] . "?action=online'>" . _AM_ONLINARTICLES . '</a><div>';
        echo "<div><a href='" . $HTTP_SERVER_VARS['PHP_SELF'] . "?action=offline'>" . _AM_OFFLIARTICLES . '</a><div>';
        echo "<div><a href='" . $HTTP_SERVER_VARS['PHP_SELF'] . "?action=noshowart'>" . _AM_NOSHOWARTICLES . '</a><div>';
        // added ladon
        echo "<div><a href='" . $HTTP_SERVER_VARS['PHP_SELF'] . "?action=spotlight'>" . _AM_SPOTLIGHTARTICLES . '</a><div>';
        echo '</td></tr></table><br>';

        echo "<table width='100%' border='0' cellpadding ='2' cellspacing='1' class='outer'>";
        echo "<tr><td class='even'>";
        //echo "<tr><td class='bg3'>";
        if ('all' == $action) {
            echo '' . _AM_ALLTXT . '</td></tr>';
        }
        if ('published' == $action) {
            echo '' . _AM_PUBLISHEDTXT . '</td></tr>';
        }
        if ('submitted' == $action) {
            echo '' . _AM_SUBMITTEDTXT . '</td></tr>';
        }
        if ('online' == $action) {
            echo '' . _AM_ONLINETXT . '</td></tr>';
        }
        if ('offline' == $action) {
            echo '' . _AM_OFFLINETXT . '</td></tr>';
        }
        if ('autoexpire' == $action) {
            echo '' . _AM_AUTOEXPIRETXT . '</td></tr>';
        }
        if ('expired' == $action) {
            echo '' . _AM_EXPIREDTXT . '</td></tr>';
        }
        if ('autoart' == $action) {
            echo '' . _AM_AUTOTXT . '</td></tr>';
        }
        if ('noshowart' == $action) {
            echo '' . _AM_NOSHOWTXT . '</td></tr>';
        }
        // added ladon
        if ('spotlight' == $action) {
            echo '' . _AM_SHOWSPOTLIGHTARTICLESTXT . '</td></tr>';
        }
        echo '</tr></td></table>';

        echo "<table border='1' width='100%' cellpadding ='2' cellspacing='1'>";
        echo "<tr class='bg3'>";
        echo "<td align='center'><b>" . _AM_STORYID . '</td>';
        echo "<td align='center'><b>" . _AM_TITLE . '</td>';
        echo "<td align='center'><b>" . _AM_CATEGORYT . '</td>';
        echo "<td align='center'><b>" . _AM_POSTER . '</td>';
        echo "<td align='center' class='nw'><b>" . _AM_STATUS . '</td>';
        echo "<td align='center' class='nw'><b>" . _AM_WEIGHT . '</td>';
        if ('all' == $action) {
            echo "<td align='center' class='nw'><b>" . _AM_CREATED . '</td>';
        }
        if ('published' == $action) {
            echo "<td align='center' class='nw'><b>" . _AM_PUBLISHED . '</td>';
        }
        if ('autoart' == $action) {
            echo "<td align='center' class='nw'><b>" . _AM_PUBLISHEDON . '</td>';
        }
        if ('autoexpire' == $action) {
            echo "<td align='center' class='nw'><b>" . _AM_EXPIRED . '</td>';
        }
        if ('expired' == $action) {
            echo "<td align='center' class='nw'><b>" . _AM_EXPARTS . '</td>';
        }
        if ('submitted' == $action) {
            echo "<td align='center' class='nw'><b>" . _AM_SUBMITTED2 . '</td>';
        }
        echo "<td align='center'><b>" . _AM_ACTION . '</td></b>';
        echo '</tr>';

        if ('0' == count($articlearray)) {
            echo "<tr ><td align='center' colspan ='10' class = 'head'><b>No Articles found</b></td></tr>";
        }

        for ($i = 0, $iMax = count($articlearray); $i < $iMax; $i++) {
            $allarticles = [];

            $allarticles['status'] = ('0' == $articlearray[$i]->offline) ? 'Online' : 'Offline';

            if ('published' == $action) {
                $allarticles['published'] = ($articlearray[$i]->published() > '0') ? formatTimestamp($articlearray[$i]->published(), (string)$wfsConfig[timestamp]) : 'Not published';
            }

            if ('all' == $action) {
                $allarticles['created'] = ($articlearray[$i]->created() > '0') ? formatTimestamp($articlearray[$i]->created(), (string)$wfsConfig[timestamp]) : 'Not published';
            }

            if ('autoart' == $action) {
                $allarticles['auto'] = ($articlearray[$i]->published() >= time()) ? formatTimestamp($articlearray[$i]->published(), (string)$wfsConfig[timestamp]) : 'Not published';
            }

            if ('autoexpire' == $action) {
                $allarticles['aexpire'] = ($articlearray[$i]->expired() > time()) ? formatTimestamp($articlearray[$i]->expired(), (string)$wfsConfig[timestamp]) : ' ----- ';
            }

            if ('expired' == $action) {
                $allarticles['expired'] = ($articlearray[$i]->expired() < time()) ? formatTimestamp($articlearray[$i]->expired(), (string)$wfsConfig[timestamp]) : ' ----- ';
            }

            if ('submitted' == $action) {
                $allarticles['submit'] = ('0' == $articlearray[$i]->published()) ? formatTimestamp($articlearray[$i]->created(), (string)$wfsConfig[timestamp]) : 'Not published';
            }

            $allarticles['weight'] = ($articlearray[$i]->weight());

            $allarticles['topic'] = $articlearray[$i]->categoryTitle();

            $allarticles['page'] = $articlearray[$i]->page();

            $allarticles['articleid'] = $articlearray[$i]->articleid();

            $allarticles['artlink'] = "<a href='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/article.php?articleid=' . $articlearray[$i]->articleid() . "'>" . $articlearray[$i]->title() . '</a>';

            $allarticles['artuser'] = "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $articlearray[$i]->uid() . "'>" . $articlearray[$i]->uname() . '</a>';

            $allarticles['edit'] = "<a href='index.php?op=edit&amp;articleid=" . $articlearray[$i]->articleid() . "'>" . _AM_EDIT . "</a>-<a href='index.php?op=delete&amp;articleid=" . $articlearray[$i]->articleid() . '&amp;page=' . $articlearray[$i]->page() . "'>" . _AM_DELETE . '</a>';

            echo "<tr><td align='center' class = 'head'><b>" . $allarticles['articleid'] . '</b>';

            echo "</td><td align='left' class = 'even'>" . $allarticles['artlink'] . '';

            echo "</td><td align='center' class = 'odd'>" . $allarticles['topic'] . '';

            echo "</td><td align='center' class = 'even'>" . $allarticles['artuser'] . '';

            echo "</td><td align='center' class = 'odd'>" . $allarticles['status'] . '';

            echo "</td><td align='center' class = 'even'>" . $allarticles['weight'] . '';

            if ('all' == $action) {
                echo "</td><td align='center' class='odd'>" . $allarticles['created'] . '';
            }

            if ('submitted' == $action) {
                echo "</td><td align='center' class='odd'>" . $allarticles['submit'] . '';
            }

            if ('published' == $action) {
                echo "</td><td align='center' class='odd'>" . $allarticles['published'] . '';
            }

            if ('autoart' == $action) {
                echo "</td><td align='center' class='odd'>" . $allarticles['auto'] . '';
            }

            if ('autoexpire' == $action) {
                echo "</td><td align='center' class='odd'>" . $allarticles['aexpire'] . '';
            }

            if ('expired' == $action) {
                echo "</td><td align='center' class='odd'>" . $allarticles['expired'] . '';
            }

            echo "</td><td align='center' class='even'>" . $allarticles['edit'] . '';

            echo '</td></tr>';

            unset($allarticles);
        }

        echo '</table><br>';

        if ($totalcount > $scount) {
            require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

            $pagenav = new XoopsPageNav($totalcount, $wfsConfig['lastart'], $start, 'start', 'lastarts=' . $xoopsOption, 1);

            echo "<div style='text-align: center;' class = 'head'>" . $pagenav->renderNav() . '</div><br>';
        } else {
            echo '';
        }
            echo'<br>';

        if ($totalcount > 1) {
            echo "<table border='0' cellpadding='1' cellspacing='1' width='100%' class = 'outer'>";

            echo "<tr><td align='center' class='even' colspan='5'>";

            $orderbyTrans = convertorderbytrans($orderby);

            echo '<small><center>' . _WFS_SORTBY1 . '&nbsp;';

            echo '&nbsp;' . _WFS_ARTICLEID1 . " (<a href='allarticles.php?orderby=articleidA'><img src='../images/up.gif' border='0' align='middle' alt=''></a><a href='allarticles.php?orderby=articleidD'><img src='../images/down.gif' border='0' align='middle' alt=''></a>)";

            echo '&nbsp;' . _WFS_TITLE1 . " (<a href='allarticles.php?orderby=titleA'><img src='../images/up.gif' border='0' align='middle' alt=''></a><a href='allarticles.php?orderby=titleD'><img src='../images/down.gif' border='0' align='middle' alt=''></a>)";

            echo '&nbsp;' . _WFS_DATE1 . " (<a href='allarticles.php?orderby=createdA'><img src='../images/up.gif' border='0' align='middle' alt=''></a><a href='allarticles.php?orderby=createdD'><img src='../images/down.gif' border='0' align='middle' alt=''></a>)";

            echo '&nbsp;' . _WFS_WEIGHT . " (<a href='allarticles.php?orderby=weight'>Reset</a>)";

            if ('offline' != $action) {
                echo '&nbsp;' . _WFS_RATING1 . " (<a href='allarticles.php?orderby=ratingA'><img src='../images/up.gif' border='0' align='middle' alt=''></a><a href=allarticles.php?orderby=ratingD><img src='../images/down.gif' border='0' align='middle' alt=''></a>)";
            }

            if ('offline' != $action) {
                echo '&nbsp;' . _WFS_POPULARITY1 . " (<a href='allarticles.php?orderby=counterA'><img src='../images/up.gif' border='0' align='middle' alt=''></a><a allarticles='index.php?orderby=counterD'><img src='../images/down.gif' border='0' align='middle' alt=''></a>)";
            }

            if ('offline' == $action) {
                echo '&nbsp;' . _WFS_SUBMIT1 . " (<a href='allarticles.php?orderby=submitA'><img src='../images/up.gif' border='0' align='middle' alt=''></a><a href='allarticles.php?orderby=submitD'><img src='../images/down.gif' border='0' align='middle' alt=''></a>)";
            }

            echo '<br><b><small>';

            printf(_WFS_CURSORTBY1, $orderbyTrans);

            $orderby = convertorderbyout($orderby);

            echo '</small></b></center>';

            echo '</td></tr></table>';
        }
wfsfooter();
xoops_cp_footer();
