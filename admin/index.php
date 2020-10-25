<?php
// WFSECTION
// Powerfull Section Module for XOOPS
//
// $Id: index.php,v 1.7 Date: 06/01/2003, Author: Catzwolf Exp $
//
// Admin Main

require __DIR__ . '/admin_header.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/wfscategory.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/wfsarticle.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/uploadfile.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/groupaccess.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/htmlcleaner.php';
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
// article

    case 'edit':
        xoops_cp_header();
        echo '<div><h4>' . _AM_ARTICLEMANAGEMENT . '</h4></div>';
        adminmenu();

        echo"<table width='100%' border='0' cellspacing='1' class='outer'>";
        echo "<tr><td class = 'bg3'><b>" . _AM_EDITARTICLE . '</b></td><tr>';
        echo "<tr><td class='even'>";
        if (!empty($articleid)) {
            $isedit = 1;

            $article = new WfsArticle($articleid);

            $article->editform();

            echo'</td></tr></table>';

            echo '<br>';
        }
        break;
    case 'Preview':
        xoops_cp_header();

        if (!empty($articleid)) {
            $article = new WfsArticle($articleid);
        } else {
            $article = new WfsArticle();
        }

        $article->loadPostVars();

        echo '<h4>' . _AM_ARTICLEPREVIEW . '</h4>';
        $article->preview('P');

        echo '<br>';
        echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class='even'>";
        $article->editform();
        echo'</td></tr></table>';

        break;
    case 'Clean':
        xoops_cp_header();

        global $xoopsModule, $maintext;

        if (!empty($articleid)) {
            $article = new WfsArticle($articleid);
        } else {
            $article = new WfsArticle();
        }

        $article->loadPostVars();
        $article->maintext = htmlcleaner::cleanup($article->maintext);
        echo $article->maintext;
        echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class='even'>";
        $article->editform();
        echo'</td></tr></table>';

        break;
    case 'Save':

            global $xoopsUser, $wfsConfig;

            if (empty($_POST['title'])) {
                xoops_cp_header();

                echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class='odd'>";

                echo _AM_NOTITLE . '<br>';

                echo'</td></tr></table>';

                break;
            }
            if (!empty($_POST['articleid'])) {
                $article = new WfsArticle($_POST['articleid']);

                if ('-1' == $_POST['changeuser']) {
                    $article->setUid($article->uid());
                } else {
                    $article->setUid($_POST['changeuser']);
                }
            } else {
                $article = new WfsArticle();

                if ('-1' == $_POST['changeuser']) {
                    $uid = $xoopsUser->getvar('uid');

                    $article->setUid($uid);
                } else {
                    $article->setUid($_POST['changeuser']);
                }

                $article->setType('admin');

                $article->setPublished(time());
            }

            $article->loadPostVars();

            if (empty($_POST['maintext']) || $_POST['ishtml']) {
                xoops_cp_header();

                echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"even\">";

                //echo $_POST['ishtml'];

                echo _AM_NOMAINTEXT . '<br>';

                echo'</td></tr></table>';

                break;
            }

            if (($article->approved) && 'admin' != $article->type()) {
                $article->setPublished(time());

                $isnew = '1';
            }

            $article->store();

            if (!empty($isnew) && $article->notifypub() && 0 != $article->uid()) {
                $poster = new XoopsUser($article->uid());

                $subject = _AM_ARTPUBLISHED;

                $message = sprintf(_AM_HELLO, $poster->uname());

                $message .= "\n\n" . _AM_YOURARTPUB . "\n\n";

                $message .= _AM_TITLEC . $article->title() . "\n" . _AM_URLC . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/article.php?articleryid=' . $article->storyid() . "\n" . _AM_PUBLISHEDC . formatTimestamp($article->published(), (string)$timestanp, 0) . "\n\n";

                $message .= $xoopsConfig['sitename'] . "\n" . XOOPS_URL . '';

                $xoopsMailer = getMailer();

                $xoopsMailer->useMail();

                $xoopsMailer->setToEmails($poster->getVar('email'));

                $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);

                $xoopsMailer->setFromName($xoopsConfig['sitename']);

                $xoopsMailer->setSubject($subject);

                $xoopsMailer->setBody($message);

                $xoopsMailer->send();
            }

        redirect_header('allarticles.php', 1, _AM_DBUPDATED);
        exit();
        break;
    case 'delete':
        if ($ok) {
            $article = new WfsArticle($articleid);

            $article->delete();

            redirect_header(XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/admin/index.php', 1, _AM_DBUPDATED);

            exit();
        }  
            xoops_cp_header();
            echo '';
            xoops_confirm(['op' => 'delete', 'articleid' => $articleid, 'ok' => 1], 'index.php', _AM_RUSUREDEL);

        break;
// attached file

        case 'fileup':

        global $wfsConfig;

        require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/uploadfile.php';
        require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/wfsfiles.php';

                $upload = new uploadfile();
                $upload->loadPostVars();
                $upload->setMode($wfsConfig['wfsmode']);
                $distfilename = $upload->doUploadToRandumFile(XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath']);

                if ($distfilename) {
                    $article = new WfsArticle($_POST['articleid']);

                    $file = new WfsFiles();

                    $file->setByUploadFile($upload);

                    $file->setFiledescript($_POST['textfiledescript']);

                    $file->setFiletext($_POST['textfilesearch']);

                    $file->setgroupid($_POST['groupid']);

                    if (empty($_POST['fileshowname'])) {
                        $file->setFileShowName($upload->getOriginalName());
                    } else {
                        $file->setFileShowName($_POST['fileshowname']);
                    }

                    $article->addFile($file);

                    redirect_header('index.php?op=edit&amp;articleid=' . $_POST['articleid'], 1, _AM_DBUPDATED);

                    exit();
                }  
                    xoops_cp_header();
                    echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class='odd'>";
                    echo '<h4>' . _AM_UPDATEFAIL . '</h4>';
                    if (!$upload->isAllowedMineType()) {
                        echo _AM_NOTALLOWEDMINETYPE . '<br>';
                    }
                    if (!$upload->isAllowedFileSize()) {
                        echo _AM_FILETOOBIG . '<br>';
                    }
                    echo'</td></tr></table>';

                break;
        case 'fileedit':
                require_once '../class/wfsfiles.php';
                $file = new WfsFiles($fileid);
                //xoops_cp_header();
                //echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class='odd'>";
                //echo "<h4>"._AM_EDITFILE."</h4>";
                $file->editform();
                //echo"</td></tr></table>";
                break;
        case 'delfile':
                require_once '../class/wfsfiles.php';
                if ($ok) {
                    $file = new WfsFiles($fileid);

                    $articleid = $file->getArticleid();

                    $file->delete();

                    redirect_header('index.php?op=edit&articleid=' . $articleid, 1, _AM_DBUPDATED);

                    exit();
                }  
                    xoops_cp_header();
                    global $xoopsConfig, $wfsConfig;

                    echo"<table width='100%' border='0' cellspacing='1'><tr><td>";
                    echo "<div class='confirmMsg'>";
                    echo '<h4>' . _AM_FILEDEL . '</h4>';
                    $file = new WfsFiles($fileid);
                    $filename = XOOPS_URL . '/' . $wfsConfig['filesbasepath'];
                    echo $filename . '/' . $file->getFileRealName() . ' (' . $file->getDownloadname() . ")\n";
                    echo '<table><tr><td><br>';
                    echo myTextForm('index.php?op=delfile&amp;fileid=' . $fileid . '&amp;ok=1', _AM_YES);
                    echo '</td><td><br>';
                    echo myTextForm('javascript:history.go(-1)', _AM_NO);
                    echo '</td></tr></table>';
                    echo '</div>';
                    echo'</td></tr></table>';

                break;
        case 'filesave':
                require_once '../class/wfsfiles.php';
                if (!empty($fileid)) {
                    $file = new WfsFiles($fileid);
                } else {
                    $file = new WfsFiles();
                }
                $file->loadPostVars();
                $file->store();
                redirect_header('wfsfilesshow.php', 1, _AM_DBUPDATED);
                exit();
                break;
// default

        case 'newarticle':
        case 'default':

        global $wfsConfig;

        // no break
        default:
                xoops_cp_header();
                echo '<div><h4>' . _AM_ARTICLEMANAGEMENT . '</h4></div>';
                adminmenu();

                echo "<table width='100%' border='0' cellpadding = '1' cellspacing='0' class='outer'>";
                echo "<tr class = 'even'><td>";
                echo "<div><a href='allarticles.php?action=all'>" . _AM_ALLARTICLES . '</a></div>';
                echo "<div><a href='allarticles.php?action=published'>" . _AM_PUBLARTICLES . '</a></div>';
                echo "<div><a href='allarticles.php?action=autoart'>" . _AM_AUTOARTICLES . '</a><div>';
                echo "<div><a href='allarticles.php?action=expired'>" . _AM_EXPIREDARTICLES . '</a><div>';
                echo "<div><a href='allarticles.php?action=autoexpire'>" . _AM_AUTOEXPIREARTICLES . '</a><div>';
                echo "<div><a href='allarticles.php?action=submitted'>" . _AM_SUBLARTICLES . '</a><div>';
                echo "<div><a href='allarticles.php?action=online'>" . _AM_ONLINARTICLES . '</a><div>';
                echo "<div><a href='allarticles.php?action=offline'>" . _AM_OFFLIARTICLES . '</a><div>';
                echo "<div><a href='allarticles.php?action=spotlight'>" . _AM_SPOTLIGHTARTICLES . '</a><div>';
                echo '</td></tr></table><br>';

                if (WfsCategory::countByArticle() > 0) {
                    echo "<table width='100%' border='0' cellspacing='1' cellpadding = '2' class='outer'><tr>";

                    echo "<td class='even'>";

                    echo "<div class= 'bg3'><h4>" . _AM_POSTNEWARTICLE . '</h4></div>';

                    $article = new WfsArticle();

                    $article->editform();

                    echo'</td></tr></table>';
                } else {
                    echo "<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class='odd'>";

                    echo '<h4>' . _AM_NOCATEGORY . '</h4>';

                    echo "<div><b><a href='category.php?op=default'>" . _AM_CATEGORYTAKEMETO . '</a></b></div>';

                    echo'</td></tr></table>';
                }
                wfsfooter();
                break;
}

xoops_cp_footer();
