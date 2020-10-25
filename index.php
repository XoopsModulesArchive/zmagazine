<?php
// $Id: index.php,v 1.3  Date: 06/01/2003, Author: Catzwolf Exp $
// Edit by ladon Date: 24/03/2004

require dirname(__DIR__, 2) . '/mainfile.php';
require XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/groupaccess.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/wfscategory.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/wfsarticle.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/wfstree.php';

function listcategory($listtype = 0)
{
    global $xoopsDB, $xoopsConfig, $xoopsModule, $myts, $wfsConfig, $orderby, $xoopsUser, $counter, $mydownloads_popular, $dataselect;

    echo"<table border='0' cellspacing='1' cellpadding ='3' width = 100%>";

    if ($wfsConfig['indexheader']) {
        echo "<tr><td colspan='4'>" . $myts->displayTarea($wfsConfig['indexheader']) . '</td></tr>';
    }

    if ($wfsConfig['showSpotlight']) {
        $sarray = WfsArticle::getAllArticle(10, 0, 0, $dataselect = '10');

        echo "<tr><td colspan='4' valign='top' ><br><h3>" . _WFS_SPOTLIGHTTITLE . '</h3></td></tr>';

        foreach ($sarray as $article) {
            if ($article->uid > 0) {
                $user = new xoopsUser($article->uid);

                if (($wfsConfig['realname']) && $user->getVar('name')) {
                    $username = $user->getVar('name');
                } else {
                    $username = $user->getVar('uname');
                }

                $username = "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $article->uid() . "'>" . $username . '</a>';
            } else {
                $username = $GLOBALS['xoopsConfig']['anonymous'];
            }

            $catid = $article->categoryid();

            $xt = new WfsCategory($catid);

            $title = $xt->title();

            $linkje = "<a href='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/article.php?articleid=' . $article->articleid() . "'>" . _WFS_READFULL . '</a>';

            $articlelink = "<a href='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/article.php?articleid=' . $article->articleid() . "'>";

            $articlelink .= '' . $article->textLink('S') . '</a>';

            $summary = $article->summary();

            echo "<tr><td colspan='4' valign='top' >" . _WFS_SECTIONTITLE . " $title</td></tr>";

            echo "<tr><td valign='top' rowspan='3'></td>"; //for article image, didn't make it yet!

            echo "<td valign='top' >$articlelink</td></tr>";

            if ($wfsConfig['showauthor']) {
                echo "<tr><td valign='top'><i>" . _WFS_AUTHER . " $username</i></td></tr>";
            }

            echo "<tr><td valign='top'>$summary.....$linkje</td></tr>";

            echo '<tr><td>&nbsp;</td></tr>';
        }
    }

    $xt = new WfsCategory();

    $maintopics = $xt->getFirstChild();

    $deps = 0;

    $listtype = (int)$listtype;

    echo "<tr><td colspan='2' valign='top' ><br><h3>" . _WFS_INDEXTITLE . '</h3></td></tr>';

    showcategory($maintopics, 0, $listtype);

    echo '</table>';
}

function showcategory($categorys, $deps = 0, $listtype = 0)
{
    global $xoopsConfig, $xoopsDB, $xoopsUser, $xoopsModule, $wfsConfig, $myts, $groupid, $listtype;

    foreach ($categorys as $onecat) {
        $num = WfsArticle::countByCategory($onecat->id());

        $link = XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/index.php?category=' . $onecat->id();

        $title = htmlspecialchars($onecat->title(), ENT_QUOTES | ENT_HTML5);

        if ($wfsConfig['shortcat']) {
            if (!XOOPS_USE_MULTIBYTES) {
                if (mb_strlen($title) >= 19) {
                    $title = mb_substr($title, 0, 18) . '...';
                }
            }
        }

        $arch = '<a href=' . $link . '>' . $title . '</a>';

        $description = htmlspecialchars($onecat->description('S'), ENT_QUOTES | ENT_HTML5);

        $sarray = WfsArticle::getAllArticle(10, 0, $onecat->id(), $dataselect = '4');

        if ($num) {
            $updated = formatTimestamp(WfsArticle::getLastChangedByCategory($onecat->id()), (string)$wfsConfig[timestamp]);
        }

        if (file_exists(XOOPS_ROOT_PATH . '/' . $wfsConfig['sgraphicspath'] . '/' . $onecat->imgurl) && !empty($onecat->imgurl)) {
            $image = '' . str_repeat('&nbsp;&nbsp;', $deps) . "<img src='" . XOOPS_URL . '/' . $wfsConfig['sgraphicspath'] . '/' . $onecat->imgurl('S') . "' width='64' hight='64'>	";
        } else {
            $image = '';
        }

        if (checkAccess($onecat->groupid)) {
            echo "<table border='0' cellspacing='1' cellpadding ='3' width = 100%>";

            echo "<tr><td width='30%' colspan='4' valign='top'>";

            if (true === $wfsConfig['showArchive']) {
                echo (string)$title;
            } else {
                echo (string)$arch;
            }

            echo '</td></tr>';

            if (true === $wfsConfig['showSpacer']) {
                echo "<tr><td align='center' valign='top' colspan='4'><img src='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/images/spacer.bmp' width='100%' height='3'></td></tr>";
            }

            echo "<tr><td rowspan='2' align='left' valign='top'>";

            if (true === $wfsConfig['sgraphicspath']) {
                echo $image;
            }

            if (($wfsConfig['showMarticles']) && !empty($listtype)) {
                echo "<td width='100%' valign='top' align='left' nowrap='nowrap'>";

                if (true === $wfsConfig['showArchive']) {
                    echo "<a href='$link'>&nbsp&nbsp&nbsp&nbsp Visit archives</a>";
                }

                foreach ($sarray as $article) {
                    if (checkAccess($article->groupid, 0)) {
                        echo '<li>';

                        echo "<a href='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/article.php?articleid=' . $article->articleid() . "'>";

                        if (true === $wfsConfig['picon']) {
                            echo '' . $article->iconLink() . '</a><br>';
                        } else {
                            echo '' . $article->textLink() . '</a><br>';
                        }
                    }
                }

                echo '</td>';
            }

            if (($wfsConfig['showMarticles']) && empty($listtype)) {
                echo "<td align='center' valign='top' class='even'>" . $num . '</td>';
            }

            if ($num) {
                $updated = formatTimestamp(WfsArticle::getLastChangedByCategory($onecat->id()), (string)$wfsConfig[timestamp]);
            }

            if ($wfsConfig['showMupdated']) {
                echo "<td align='right' valign='top' width ='12%' >";

                if ($num) {
                    echo $updated;
                }

                '</td>';
            }

            echo '</tr><br>';

            $childcat = $onecat->getFirstChild();

            if ('1' == $wfsConfig['submenus']) {
                if ($childcat) {
                    showcategory($childcat, $deps + 2, $listtype);
                }
            }
        }
    }
}

function listArticle($catid, $start = 0, $num = 20)
{
    global $xoopsDB, $orderby, $xoopsConfig, $xoopsUser, $xoopsModule, $wfsConfig, $myts, $counter, $mydownloads_popular, $dataselect;

    $xt = new WfsCategory($catid);

    if (file_exists(XOOPS_ROOT_PATH . '/' . $wfsConfig['sgraphicspath'] . '/' . $xt->imgurl) && 'blank.gif' != $xt->imgurl) {
        $image = "<img src='" . XOOPS_URL . '/' . $wfsConfig['sgraphicspath'] . '/' . $xt->imgurl('S') . "'>";
    } else {
        if ($xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid()) && 'blank.gif' != $xt->imgurl) {
            $image = 'ERROR: Please check path/file for image';
        } else {
            $image = '';
        }
    }

    $title = $xt->title();

    $catdescription = $xt->catdescription('S');

    echo "<table border='0' cellpadding='2' cellspacing='1' valign='top' align = 'center' width = '100%'>";

    echo "<tr><td colspan='5' align='left'><h3>" . $title . '</h3></td>';

    if ((!empty($xt->imgurl) && 1 == $xt->displayimg)) {
        echo "<td rowspan='2' align='center' valign='top'>" . $image . '</td></tr>';
    }

    if (true === $wfsConfig['showSpacer']) {
        echo "<tr><td align='center' valign='top' width='100%'><img src='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/images/spacer.bmp' width='100%' height='3'></td></tr>";
    }

    echo "<tr><td colspan='5'><br>$catdescription<br></td></tr>";

    echo '</table>';

    $sarray = WfsArticle::getAllArticle($num, $start, $catid, $dataselect = '4');

    $articlecount = WfsArticle::countByCategory($catid);

    echo "<table border='0' colspan='1' cellpadding='2' cellspacing='1' width = '100%'>";

    if (0 != $articlecount) {
        foreach ($sarray as $article) {
            $counter = $article->counter();

            $time = $article->created();

            $stat = $article->changed();

            $articlelink = "<a href='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/article.php?articleid=' . $article->articleid() . "'>";

            if ($wfsConfig['picon']) {
                $articlelink .= '' . $article->iconLink('S') . '</a>';
            } else {
                $articlelink .= '' . $article->textLink('S') . '</a>';
            }

            $summary = $article->summary();

            $published = formatTimestamp($article->published(), $wfsConfig['timestamp']);

            $counter = $article->counter();

            if ($wfsConfig['comments']) {
                $commentcount = $article->getCommentsCount();
            }

            $attachedfiles = $article->getFilesCount();

            if ($article->uid > 0) {
                $user = new xoopsUser($article->uid);

                if (($wfsConfig['realname']) && $user->getVar('name')) {
                    $username = $user->getVar('name');
                } else {
                    $username = $user->getVar('uname');
                }

                $username = "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $article->uid() . "'>" . $username . '</a>';
            } else {
                $username = $GLOBALS['xoopsConfig']['anonymous'];
            }

            if ($wfsConfig['novote']) {
                $rating = number_format($article->rating, 2);
            }

            $groupid = $article->groupid;

            if ($wfsConfig['novote']) {
                $votes = htmlspecialchars($article->votes, ENT_QUOTES | ENT_HTML5);
            }

            $status = 1;

            $orderbyTrans = convertorderbytrans($orderby);

            $linkje = "<a href='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/article.php?articleid=' . $article->articleid() . "'>read full story</a>";

            if ($stat != $time) {
                $status = 2;
            }

            if (checkAccess($groupid)) {
                echo '<tr>';

                echo "<td valign='top' width='100%' class='even' colspan='7'><li>$articlelink";

                if ($wfsConfig['noicons']) {
                    popgraphic($counter);

                    newdownloadgraphic($time, $status);
                }

                echo '</td></tr><tr>';

                if ($wfsConfig['showauthor']) {
                    echo "<td valign='top' colspan='7'><i>" . _WFS_AUTHER . " $username</i></td>";
                }

                echo '</tr>';

                if ($wfsConfig['summary']) {
                    echo "<tr><td valign='top' width='100%' colspan='7'>$summary.....$linkje</td</tr>";
                }

                echo '<tr><td>&nbsp;</td></tr>';

                echo '<tr>';

                if ($wfsConfig['showhits']) {
                    echo "<td align='left'  valign='top'><b>" . _WFS_HITS . ": $counter</b></td>";
                }

                if ($wfsConfig['showcomments']) {
                    if ($wfsConfig['comments']) {
                        echo "<td align='left'  valign='top'><b>" . _WFS_COMMENT . ": $commentcount</b></td>";
                    }
                }

                if ($wfsConfig['showfile']) {
                    echo "<td align='left'  valign='top'><b>" . _WFS_FILES . ": $attachedfiles</b></td>";
                }

                if ($wfsConfig['novote']) {
                    if ($wfsConfig['showrated']) {
                        echo "<td align='left'  valign='top'><b>" . _WFS_RATED . ": $rating</b></td>";
                    }

                    if ($wfsConfig['showvotes']) {
                        echo "<td align='left'  valign='top'><b>" . _WFS_VOTES . ": $votes</b></td>";
                    }
                }

                if ($wfsConfig['showupdated']) {
                    echo "<td align='center' nowrap='nowrap' valign='top'><b>" . _WFS_PUBLISHEDHOME . ": $published</b></td>";
                }

                echo '</tr>';

                echo '<tr><td>&nbsp;</td></tr>';
            }
        } //end check access
    }

    echo '</table>';

    if ($articlecount > $num) {
        echo "<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center' valign='top'><tr><td align='center'>";

        if ($articlecount < $start + $num) {
            echo "<a href='index.php?category=" . $catid . '&amp;start=' . ($start - $num) . "'>" . _WFS_PREVPAGE . '</a>&nbsp;';
        }

        if ($articlecount > $start + $num) {
            echo "<a href='index.php?category=" . $catid . '&amp;start=' . ($start + $num) . "'>" . _WFS_NEXTPAGE . '</a>&nbsp;';
        }

        for ($i = 0, $j = 1; $i <= $articlecount; $i += $num, $j++) {
            if (($i <= $start) && ($start < ($i + $num))) {
                echo $j . '&nbsp;';
            } else {
                echo "<a href='index.php?category=" . $catid . '&amp;start=' . ($i) . "'>" . ($j) . '</a>&nbsp;';
            }
        }

        echo '</td></tr></table>';
    }

    echo "<table cellpadding='2' cellspacing='1' width='100%'>";

    if ($xt->catfooter) {
        echo '<tr><td><br>' . $xt->catfooter('S') . '<br><br></td></tr>';
    }

    echo '</table>';
}

// Start of WF-Section here
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

if (isset($_GET['orderby'])) {
    $orderby = convertorderbyin($_GET['orderby']);
} else {
    // -- Skalpack2 [start]

    //$orderby = 'title ASC'];

    $orderby = $wfsConfig['aidxorder'];

    // -- Skalpack2 [/end]
}

$listtype = $wfsConfig['toppagetype'];

if (isset($_GET['listtype'])) {
    $listtype = (int)$_GET['listtype'];
}

require dirname(__DIR__, 2) . '/header.php';

if (empty($category)) {
    listcategory($listtype);
} else {
    listArticle($category, $start, $wfsConfig['articlesapage']);
}

require_once __DIR__ . '/footer.php';
