<?php
// $Id: submit.php,v 1.9 2003/04/01 22:51:21 mvandam Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <https://www.xoops.org>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
require dirname(__DIR__, 2) . '/mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/wfsarticle.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/common.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/groupaccess.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/wfsfiles.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/uploadfile.php';
//require_once XOOPS_ROOT_PATH."/modules/".$xoopsModule->dirname()."/cache/uploadconfig.php";

global $wfsConfig;

foreach ($_POST as $k => $v) {
    ${$k} = $v;
}

foreach ($_GET as $k => $v) {
    ${$k} = $v;
}

if (empty($wfsConfig['anonpost']) && !is_object($xoopsUser)) {
    redirect_header('index.php', 1, _NOPERM);

    exit();
}
$op = 'form';

if (isset($_POST['preview'])) {
    $op = 'preview';
} elseif (isset($_POST['post'])) {
    $op = 'post';
} elseif (isset($_POST['edit'])) {
    $op = 'edit';
}
if (isset($_GET['op'])) {
    $op = $_GET['op'];
}
if (isset($_POST['op'])) {
    $op = $_POST['op'];
}

switch ($op) {
case 'preview':
        $myts = MyTextSanitizer::getInstance(); // MyTextSanitizer object
        $xt = new WfsCategory($xoopsDB->prefix('zmag_category'), $_POST['id']);
        include  XOOPS_ROOT_PATH . '/header.php';

        $p_subject = htmlspecialchars($subject, ENT_QUOTES | ENT_HTML5);
        if ($xoopsUser && $xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
            $nohtml = isset($nohtml) ? (int)$nohtml : 0;
        } else {
            $nohtml = 1;
        }
        $html = empty($nohtml) ? 1 : 0;
        if (isset($nosmiley) && (int)$nosmiley > 0) {
            $nosmiley = 1;

            $smiley = 0;
        } else {
            $nosmiley = 0;

            $smiley = 1;
        }
        $p_message = $myts->previewTarea($message, $html, $smiley, 1);
        $subject = htmlspecialchars($subject, ENT_QUOTES | ENT_HTML5);
        $message = htmlspecialchars($message, ENT_QUOTES | ENT_HTML5);
        $noname = isset($noname) ? (int)$noname : 0;
        $notifypub = isset($notifypub) ? (int)$notifypub : 0;
        themecenterposts($p_subject, $p_message);
        require __DIR__ . '/include/storyform.inc.php';
        require XOOPS_ROOT_PATH . '/footer.php';
        break;
case 'post':
        $nohtml_db = 1;
        if ($xoopsUser) {
            $uid = $xoopsUser->getVar('uid');

            if ($xoopsUser->isAdmin($xoopsModule->mid())) {
                $nohtml_db = empty($nohtml) ? 0 : 1;
            }
        } else {
            if (1 == $wfsConfig['anonpost']) {
                $uid = 0;
            } else {
                redirect_header('index.php', 3, _NW_ANONNOTALLOWED);

                exit();
            }
        }
        $story = new WfsArticle();
        $story->setTitle($subject);
        $story->setMaintext($message);
        $story->setSummary($summary);
        $story->setUid($uid);
        $story->setCategoryid($id);
        $story->setNohtml($nohtml_db);
        $nosmiley = isset($nosmiley) ? (int)$nosmiley : 0;
        $notifypub = isset($notifypub) ? (int)$notifypub : 0;
        $story->setHtmlpage('');
        $story->setIshtml(0);
        $story->setWeight(100);
        //$story->setGroupid($groupid);
        $story->setGroupid($groupid);
        $story->setNosmiley($nosmiley);
        $story->setPublished(0);
        $story->setExpired(0);
        $story->setNotifypub($notifypub);
        $story->setSpotlight($spotlight);
        echo $story->articleid;

        $story->setType('user');

        $upload = new uploadfile($_POST['filename']);
        $distfilename = $upload->doUploadToRandumFile(XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath']);

        if ($distfilename) {
            $article = new WfsArticle($story->articleid);

            $file = new WfsFiles();

            $file->setByUploadFile($_POST['filename']);

            if (empty($_POST['downloadfilename'])) {
                $file->setFileShowName($_POST['filename']);
            } else {
                $file->setFileShowName($_POST['$downloadfilename']);
            }

            $article->addFile($_POST['filename']);
        }

        if (1 == $wfsConfig['autoapprove']) {
            $approve = 1;

            $story->setApproved($approve);

            $story->setPublished(time());

            $story->setExpired(0);
        }
        $result = $story->store();
        if ($result) {
            if (1 == $wfsConfig['notifysubmit']) {
                $xoopsMailer = getMailer();

                $xoopsMailer->useMail();

                $xoopsMailer->setToEmails($xoopsConfig['adminmail']);

                $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);

                $xoopsMailer->setFromName($xoopsConfig['sitename']);

                $xoopsMailer->setSubject(_NW_NOTIFYSBJCT);

                $body = _NW_NOTIFYMSG;

                $body .= "\n\n" . _NW_TITLE . ': ' . $story->title();

                $body .= "\n" . _POSTEDBY . ': ' . XoopsUser::getUnameFromId($uid);

                $body .= "\n" . _DATE . ': ' . formatTimestamp(time(), 'm', $xoopsConfig['default_TZ']);

                $body .= "\n\n" . XOOPS_URL . '/modules/zmagazine/admin/index.php?op=edit&articleid=' . $result;

                $xoopsMailer->setBody($body);

                $xoopsMailer->send();
            }
        } else {
            echo 'error';
        }
        redirect_header('index.php', 2, _WFS_THANKS);
        break;
case 'edit':

    require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/wfscategory.php';
    require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/wfsarticle.php';

        $story = new WfsArticle('articleid');
        $subject = $story->title();
        $message = $story->maintext();
        $summary = $story->summary('Edit');
        $url = $story->url();
        $urlname = $story->urlname();
        //$story->Uid();
        //$story->setCategoryid($id);
        //$story->setNohtml($nohtml_db);
        // added ladon V2
        $story->setSpotlight($spotlight);
        $nosmiley = isset($nosmiley) ? (int)$nosmiley : 0;
        $notifypub = isset($notifypub) ? (int)$notifypub : 0;
        $story->setHtmlpage('');
        $story->setIshtml(0);
        //$story->setGroupid($groupid);
        //$story->setNosmiley($nosmiley);
        //$story->setPublished(0);
        //$story->setExpired(0);
        //$story->setNotifyPub($notifypub);
        //$story->setType('user');

        $xt = new WfsCategory($xoopsDB->prefix('zmag_article '), 14);

        $noname = 0;
        $nohtml = 0;
        $nosmiley = 0;
        $notifypub = 0;
        // added ladon V2
        $spotlight = 0;

        require XOOPS_ROOT_PATH . '/header.php';
        indexmainheader();
        require __DIR__ . '/include/storyform.inc.php';
        require XOOPS_ROOT_PATH . '/footer.php';

break;
case 'form':
default:
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/wfscategory.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/wfsarticle.php';

        $xt = new WfsCategory($xoopsDB->prefix('zmag_category'));
        $num = WfsArticle::countByCategory($xt->id);
        require XOOPS_ROOT_PATH . '/header.php';
        $subject = '';
        $message = '';
        $groupid = '';
        $summary = '';
        $url = '';
        $urlname = '';
        $filename = '';
        $downloadfilename = '';
        $noname = 0;
        $nohtml = 0;
        $nosmiley = 0;
        $notifypub = 1;
        // added ladon v2
        $spotlight = 0;
        indexmainheader();
        require __DIR__ . '/include/storyform.inc.php';

        require XOOPS_ROOT_PATH . '/footer.php';
        break;
}
