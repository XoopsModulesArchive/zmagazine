<?php
// $Id: wfsarticle.php,v 1.7 Date: 06/01/2003, Author: Catzwolf Exp $
// edit ladon, added option to show article in spotlight section!

require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/wfscategory.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/wfsfiles.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/uploadfile.php';
//require_once XOOPS_ROOT_PATH."/modules/".$xoopsModule->dirname()."/cache/uploadconfig.php";
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/wysiwygeditor.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/functions.php';
//require_once XOOPS_ROOT_PATH.'/modules/'.$xoopsModule->dirname().'/include/htmlcleaner.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/common.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/mimetype.php';
require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
require_once XOOPS_ROOT_PATH . '/class/xoopscomments.php';

//Global $wfsConfig;
$myts = MyTextSanitizer::getInstance();

class WfsArticle
{
    public $db;

    public $table;

    public $commentstable;

    public $categorytable;

    public $filestable;

    public $articleid;

    public $categoryid;

    public $uid;

    public $title;

    public $maintext;

    public $counter;

    public $created;

    public $changed;

    public $nohtml = 0;

    public $nosmiley = 0;

    public $summary;

    public $url;

    public $urlname;

    public $page = 1;

    public $groupid;

    public $rating;

    public $votes;

    public $popular;

    public $notifypub;

    public $type;

    public $approved;

    public $htmlpage;

    public $ishtml;

    public $groupid;

    public $offline;

    public $weight;

    public $approved;

    public $changeuser;

    public $hostname;

    public $noshowart;

    // class instance

    public $category;

    public $files;

    // temp

    public $fileshowname;

    // flag

    public $titleFlag;

    public $maintextFlag;

    public $summaryFlag;

    // spotlight added ladon V3

    public $spotlight;

    // constructor

    public function __construct($articleid = -1)
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();

        $this->table = $this->db->prefix('zmag_article');

        $this->commentstable = $this->db->prefix('wfs_comments');

        $this->categorytable = $this->db->prefix('zmag_category');

        $this->filestable = $this->db->prefix('zmag_files');

        $this->titleFlag = 0;

        $this->maintextFlag = 0;

        $this->summaryFlag = 0;

        if (is_array($articleid)) {
            $this->makeArticle($articleid);

            $this->category = $this->category();
        } elseif (-1 != $articleid) {
            $this->getArticle($articleid);

            $this->category = $this->category();
        }
    }

    public function loadArticle($id)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE articleid=' . $id . ' and published < ' . time() . ' AND published > 0 AND (expired = 0 OR expired > ' . time() . ') AND offline = 0 AND nowshow = 1';

        //$sql = "SELECT * FROM ".$this->table." WHERE articleid=".$id." and published > 0 and offline ='0' ";

        $array = $this->db->fetchArray($this->db->query($sql));

        $this->makeArticle($array);
    }

    // create instance of other classes

    public function category()
    {
        return new WfsCategory($this->categoryid);
    }

    // set property

    public function setArticleid($value)
    {
        $this->articleid = $value;
    }

    public function setCategoryid($value)
    {
        $this->categoryid = $value;

        if (!isset($this->category)) {
            $this->category = $this->category();
        }
    }

    public function setUid($value)
    {
        $this->uid = $value;
    }

    public function setTitle($value)
    {
        $this->title = $value;

        $this->titleFlag = 1;
    }

    public function setMaintext($value)
    {
        $this->maintext = $value;

        //$this->maintext = htmlcleaner::cleanup($value);

        $this->maintext = preg_replace('/<P>/', '<P style="margin: 0.4cm 0cm 0pt">', $this->maintext);

        $this->maintext = preg_replace('/<DIV style="margin: 0.4cm 0cm 0pt">/', '<DIV style="MARGIN: 0.0cm 0cm 0pt">', $this->maintext);

        $this->maintext = preg_replace('<TBODY>', '', $this->maintext);

        $this->maintext = preg_replace('</TBODY>', '', $this->maintext);

        $this->maintextFlag = 1;
    }

    public function setNohtml($value)
    {
        $this->nohtml = $value;
    }

    public function setNosmiley($value)
    {
        $this->nosmiley = $value;
    }

    public function setFileshowname($value)
    {
        $this->fileshowname = $value;
    }

    public function setSummary($value)
    {
        $this->summary = $value;

        $this->summaryFlag = 1;
    }

    public function setUrl($value)
    {
        $this->url = $value;
    }

    public function setUrlname($value)
    {
        $this->urlname = $value;
    }

    public function setPage($value)
    {
        $this->page = $value;
    }

    public function setNotifypub($value)
    {
        $this->notifypub = $value;
    }

    public function setType($value)
    {
        $this->type = $value;
    }

    public function setPublished($value)
    {
        $this->published = $value;
    }

    public function setExpired($value)
    {
        $this->expired = $value;
    }

    public function setApproved($value)
    {
        $this->approved = $value;
    }

    public function setHtmlpage($value)
    {
        $this->htmlpage = $value;
    }

    public function setIshtml($value)
    {
        $this->ishtml = $value;
    }

    public function setGroupid($value)
    {
        $this->groupid = saveAccess($value);
    }

    public function setOffline($value)
    {
        $this->offline = $value;
    }

    public function setWeight($value)
    {
        $this->weight = $value;
    }

    public function setChangeuser($value)
    {
        $this->changeuser = $value;
    }

    public function setNoshowart($value)
    {
        $this->nowshowart = $value;
    }

    // added ladon

    public function setSpotlight($value)
    {
        $this->spotlight = $value;
    }

    // $file : WfsFile class instance

    public function addFile($file = '')
    {
        $file->setArticleid($this->articleid);

        $file->store();

        $this->store();
    }

    // database

    public function store($timestamp = '')
    {
        global $_POST, $groupid, $myts, $xoopsDB, $xoopsConfig;

        $myts = MyTextSanitizer::getInstance();

        $title = $myts->censorString($this->title);

        $maintext = $myts->censorString($this->maintext);

        $summary = $myts->censorString($this->summary);

        $title = $myts->addSlashes($title);

        $maintext = $myts->addSlashes($maintext);

        $summary = $myts->addSlashes($summary);

        $url = $myts->addSlashes($this->url);

        $urlname = $myts->addSlashes($this->urlname);

        $page = $myts->addSlashes($this->page);

        $type = $myts->addSlashes($this->type);

        $offline = $myts->addSlashes($this->offline);

        $htmlpage = $myts->addSlashes($this->htmlpage);

        $ishtml = $myts->addSlashes($this->ishtml);

        $published = $myts->addSlashes($this->published);

        $expired = $myts->addSlashes($this->expired);

        $notifypub = $myts->addSlashes($this->notifypub);

        $userid = $myts->addSlashes($this->changeuser);

        $hostname = $myts->addSlashes($this->hostname);

        $weight = $myts->addSlashes($this->weight);

        $noshowart = $myts->addSlashes($this->noshowart);

        $userid = $myts->addSlashes($this->uid);

        //added by ladon

        $spotlight = $myts->addSlashes($this->spotlight);

        if (empty($groupid)) {
            $groupid = '';
        } else {
            $groupid = $myts->addSlashes($this->groupid);
        }

        if (!isset($this->nohtml) || 1 != $this->nohtml) {
            $this->nohtml = 0;
        }

        if (!isset($this->nosmiley) || 1 != $this->nosmiley) {
            $this->nosmiley = 0;
        }

        if (!isset($this->categoryid)) {
            $this->categoryid = 0;
        }

        if (!isset($this->page)) {
            $this->page = 1;
        }

        if (!isset($this->articleid)) {
            $newarticleid = $this->db->genId($this->table . '_articleid_seq');

            $created = ($created = time());

            $changed = $created;

            $sql = 'INSERT INTO ' . $this->table .
                        ' (articleid, uid, title, created, changed, nohtml, nosmiley, maintext, counter, categoryid, summary, url, groupid, published, type, notifypub, urlname, htmlpage, ishtml, offline, page, weight, noshowart, spotlight) ' .
                        'VALUES (' . $newarticleid . ',' . $userid . ",'" . $title . "'," . $created . ',' . $changed . ',' . $this->nohtml . ',' . $this->nosmiley . ",'" . $maintext . "',0," .
                        $this->categoryid . ",'" . $summary . "','" . $url . "','" . $groupid . "','" . $published . "', '" . $type . "', '" . $notifypub . "', '" . $urlname . "', '" . $htmlpage . "', " . $this->ishtml . ", '" . $offline . "', '" . $page . "', '" . $weight . "', '" . $noshowart . "', '" . spotlight . "')";
        } else {
            $this->changed = time();

            $sql = 'UPDATE ' . $this->table .
                        ' SET
						uid=' . $userid . ",
						title='" . $title . "',
						changed=" . $this->changed . ',
						nohtml=' . $this->nohtml . ',
						nosmiley=' . $this->nosmiley . ",
						maintext='" . $maintext . "',
						categoryid=" . $this->categoryid . ",
						summary='" . $summary . "',
						url='" . $url . "',
						groupid='" . $groupid . "',
						published='" . $published . "',
						expired='" . $expired . "',
						offline='" . $offline . "',
						urlname='" . $urlname . "',
						ishtml=" . $this->ishtml . ",
						page='" . $page . "',
						weight='" . $weight . "',
						htmlpage='" . $htmlpage . "',
						noshowart ='" . $noshowart . "',
						spotlight ='" . $spotlight . "'  
						" . ' WHERE articleid=' . $this->articleid . '';
        }

        if (!$result = $this->db->query($sql)) {
            return false;
        }

        return true;
    }

    public function getArticle($articleid)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE articleid=' . $articleid . ' ';

        $array = $this->db->fetchArray($this->db->query($sql));

        if (0 == count($array)) {
            return false;
        }

        $this->makeArticle($array);
    }

    public function makeArticle($array)
    {
        foreach ($array as $key => $value) {
            $this->$key = $value;
        }

        $this->files = WfsFiles::getAllbyArticle($this->articleid);
    }

    public function delete()
    {
        global $xoopsDB, $_POST, $_GET, $xoopsConfig, $xoopsModule;

        $sql = 'DELETE FROM ' . $this->table . ' WHERE articleid=' . $this->articleid . '';

        if (!$result = $this->db->query($sql)) {
            return false;
        }

        if (isset($this->commentstable) && '' != $this->commentstable) {
            xoops_comment_delete($xoopsModule->getVar('mid'), $this->articleid);
        }

        if (isset($this->filestable) && '' != $this->filestable) {
            $this->files = WfsFiles::getAllbyArticle($this->articleid);

            foreach ($this->files as $file) {
                $file->delete();
            }
        }

        return true;
    }

    public function updateCounter()
    {
        $sql = 'UPDATE ' . $this->table . ' SET counter=counter+1 WHERE articleid=' . $this->articleid . '';

        if (!$result = $this->db->queryF($sql)) {
            return false;
        }

        return true;
    }

    // get

    public function categoryid()
    {
        return $this->categoryid;
    }

    public function categoryTitle()
    {
        return $this->category->title();
    }

    public function uid()
    {
        return $this->uid;
    }

    public function uname()
    {
        global $xoopsUser;

        return XoopsUser::getUnameFromId($this->uid);
    }

    public function title($format = 'Show')
    {
        $myts = MyTextSanitizer::getInstance();

        $smiley = 1;

        if ($this->nosmiley()) {
            $smiley = 0;
        }

        switch ($format) {
                case 'S':
                case 'Show':
                    $title = htmlspecialchars($this->title, $smiley);
                break;
                case 'E':
                case 'Edit':
                    $title = htmlspecialchars($this->title, ENT_QUOTES | ENT_HTML5);
                break;
                case 'P':
                        case 'Preview':
                                $title = htmlspecialchars($this->title, $smiley);
                                break;
                        case 'F':
                        case 'InForm':
                                $title = htmlspecialchars($this->title, ENT_QUOTES | ENT_HTML5);
                                break;
                }

        return $title;
    }

    public function maintext($format = 'Show', $page = -1)
    {
        global $xoopsModule;

        $myts = MyTextSanitizer::getInstance();

        $html = 1;

        $smiley = 1;

        $xcodes = 1;

        if ($this->nohtml()) {
            $html = 0;
        }

        if ($this->nosmiley()) {
            $smiley = 0;
        }

        if (-1 == $page) {
            $maintext = $this->maintext;
        } else {
            $maintextarr = explode('[pagebreak]', $this->maintext);

            if ($page > count($maintextarr)) {
                $maintext = $maintextarr[count($maintextarr)];
            } else {
                $maintext = $maintextarr[$page];
            }
        }

        switch ($format) {
                        case 'S':
                        case 'Show':
                                $maintext = $myts->displayTarea($maintext, $html, $smiley, $xcodes);
                                break;
                        case 'E':
                        case 'Edit':
                                $maintext = htmlspecialchars($maintext, ENT_QUOTES | ENT_HTML5);
                                break;
                        case 'P':
                        case 'Preview':
                                $maintext = $myts->previewTarea($maintext, $html, $smiley, $xcodes);
                                break;
                        case 'F':
                        case 'InForm':
                                $maintext = htmlspecialchars($maintext, ENT_QUOTES | ENT_HTML5);
                                break;
                }

        return $maintext;
    }

    public function maintextPages()
    {
        $maintextarr = explode('[pagebreak]', $this->maintext);

        return count($maintextarr);
    }

    public function maintextWithFile($format = 'Show', $page = '')
    {
        global $xoopsModule;

        $maintext = $this->maintext($format, $page);

        return $maintext;
    }

    public function summary($format = 'Show')
    {
        $myts = MyTextSanitizer::getInstance();

        $html = 1;

        $smiley = 1;

        $xcodes = 1;

        $spot = 1;

        if ($this->nohtml()) {
            $html = 0;
        }

        if ($this->nosmiley()) {
            $smiley = 0;
        }

        $summary = $this->summary;

        switch ($format) {
                        case 'S':
                        case 'Show':
                                $summary = $myts->displayTarea($summary, $html, $smiley, $xcodes);
                                break;
                        case 'E':
                        case 'Edit':
                                $summary = htmlspecialchars($summary, ENT_QUOTES | ENT_HTML5);
                                break;
                        case 'P':
                        case 'Preview':
                                $summary = $myts->previewTarea($summary, $html, $smiley, $xcodes);
                                break;
                        case 'F':
                        case 'InForm':
                                $summary = htmlspecialchars($summary, ENT_QUOTES | ENT_HTML5);
                                break;
                }

        return $summary;
    }

    public function url($format = 'Show')
    {
        $myts = MyTextSanitizer::getInstance();

        switch ($format) {
                        case 'S':
                        case 'Show':
                                $title = htmlspecialchars($this->url, 0);
                                break;
                        case 'E':
                        case 'Edit':
                                $title = htmlspecialchars($this->url, ENT_QUOTES | ENT_HTML5);
                                break;
                        case 'P':
                        case 'Preview':
                                $title = htmlspecialchars($this->url, 0);
                                break;
                        case 'F':
                        case 'InForm':
                                $title = htmlspecialchars($this->url, ENT_QUOTES | ENT_HTML5);
                                break;
                }

        return $title;
    }

    public function counter()
    {
        return $this->counter;
    }

    public function created()
    {
        return $this->created;
    }

    public function urlname()
    {
        return $this->urlname;
    }

    public function htmlpage()
    {
        return $this->htmlpage;
    }

    public function ishtml()
    {
        return $this->ishtml;
    }

    public function changed()
    {
        return $this->changed;
    }

    public function articleid()
    {
        return $this->articleid;
    }

    public function nohtml()
    {
        return $this->nohtml;
    }

    public function nosmiley()
    {
        return $this->nosmiley;
    }

    public function page()
    {
        return $this->page;
    }

    public function notifypub()
    {
        return $this->notifypub;
    }

    public function type()
    {
        return $this->type;
    }

    public function published()
    {
        return $this->published;
    }

    public function expired()
    {
        return $this->expired;
    }

    public function groupid()
    {
        return $this->groupid;
    }

    public function offline()
    {
        return $this->offline;
    }

    public function weight()
    {
        return $this->weight;
    }

    public function approved()
    {
        return $this->approved;
    }

    public function changeuser()
    {
        return $this->changeuser;
    }

    public function noshowart()
    {
        return $this->noshowart;
    }

    // added ladon

    public function spotlight()
    {
        return $this->spotlight;
    }

    public function getCommentsCount()
    {
        global $xoopsDB, $_POST, $_GET, $xoopsConfig, $xoopsModule;

        $count = xoops_comment_count($xoopsModule->getVar('mid'), $this->articleid);

        return $count;
    }

    public function getFilesCount()
    {
        if (empty($this->articleid)) {
            return 0;
        }

        $this->files = WfsFiles::getAllbyArticle($this->articleid);

        return @count($this->files);
    }

    public function getNicePathToPid($funcURL)
    {
        $ret = $category->getNicePathToPid($funcURL);

        return $ret;
    }

    // public - WfsArticle::* style

    public function getAllArticle($limit, $start, $category, $dataselect, $asobject = true)
    {
        global $orderby;

        $db = XoopsDatabaseFactory::getDatabaseConnection();

        $myts = MyTextSanitizer::getInstance();

        $ret = [];

        if ('1' == $dataselect) { //all published articles
            $sql = 'SELECT * FROM ' . $db->prefix('zmag_article') . ' where published <= ' . time() . ' and expired = 0';
        }

        if ('2' == $dataselect) { //submitted articles
            $sql = 'SELECT * FROM ' . $db->prefix('zmag_article') . " where published = '0' and offline != '1'";
        }

        if ('3' == $dataselect) { //Gets all articles
            $sql = 'SELECT * FROM ' . $db->prefix('zmag_article') . ' ';
        }

        if ('4' == $dataselect) { //online articles
            $sql = 'SELECT * FROM ' . $db->prefix('zmag_article') . ' where (published > 0 AND published <= ' . time() . ") AND noshowart = 0 AND offline = '0' AND (expired = 0 OR expired > " . time() . ') ';
        }

        if ('5' == $dataselect) { //offline articles
            $sql = 'SELECT * FROM ' . $db->prefix('zmag_article') . " where published > 0 and offline = '1'";
        }

        if ('6' == $dataselect) { //autoexpired articles
            $sql = 'SELECT * FROM ' . $db->prefix('zmag_article') . ' where expired > ' . time() . '';
        }

        if ('7' == $dataselect) { //auto published articles
            $sql = 'SELECT * FROM ' . $db->prefix('zmag_article') . ' where published > ' . time() . '';
        }

        if ('8' == $dataselect) { //expired articles
            $sql = 'SELECT * FROM ' . $db->prefix('zmag_article') . ' where expired > 0 and expired < ' . time() . ' ';
        }

        if ('9' == $dataselect) { //expired articles
            $sql = 'SELECT * FROM ' . $db->prefix('zmag_article') . ' where noshowart = 1 ';
        }

        // added by ladon
                if ('10' == $dataselect) { //spotlight articles
                $sql = 'SELECT * FROM ' . $db->prefix('zmag_article') . ' where spotlight = 1 AND (published > 0 AND published <= ' . time() . ") AND noshowart = 0 AND offline = '0' AND (expired = 0 OR expired > " . time() . ') ';
                }

        if (!empty($category)) {
            $sql .= " and categoryid=$category ";
        }

        $sql .= ' ORDER BY ' . $orderby . '';

        $result = $db->query($sql, $limit, $start);

        while ($myrow = $db->fetchArray($result)) {
            if ($asobject) {
                $ret[] = new self($myrow);
            } else {
                $ret[$myrow['articleid']] = htmlspecialchars($myrow['title'], ENT_QUOTES | ENT_HTML5);
            }
        }

        return $ret;
    }

    public function getByCategory($categoryid)
    {
        $db = XoopsDatabaseFactory::getDatabaseConnection();

        $ret = [];

        $result = $db->query('SELECT * FROM ' . $db->prefix('zmag_article') . " WHERE categoryid=$categoryid ORDER BY " . $categoryid . '');

        while ($myrow = $db->fetchArray($result)) {
            if ('1' == checkAccess($groupid)) {
                $ret[] = new self($myrow);
            }
        }

        return $ret;
    }

    public function countByCategory($categoryid = 0)
    {
        $count = 0;

        $db = XoopsDatabaseFactory::getDatabaseConnection();

        $sql = 'SELECT * FROM ' . $db->prefix('zmag_article') . ' WHERE published < ' . time() . ' AND published > 0 AND (expired = 0 OR expired > ' . time() . ') AND offline = 0';

        if (0 != $categoryid) {
            $sql .= " and categoryid=$categoryid ";
        }

        $result = $db->query($sql);

        while ($myrow = $db->fetchArray($result)) {
            $groupid = $myrow['groupid'];

            if ('1' == checkAccess($groupid)) {
                $count++;
            }
        }

        return $count;
    }

    public function getLastChangedByCategory($categoryid = 0)
    {
        $db = XoopsDatabaseFactory::getDatabaseConnection();

        $sql = 'SELECT MAX(changed) FROM ' . $db->prefix('zmag_article') . ' WHERE published < ' . time() . ' AND published > 0 AND (expired = 0 OR expired > ' . time() . ') AND offline = 0';

        if (0 != $categoryid) {
            $sql .= " AND categoryid=$categoryid ";
        }

        $result = $db->query($sql);

        [$count] = $db->fetchRow($result);

        return $count;
    }

    // HTML

    public function textLink($format = 'Show')
    {
        global $xoopsModule, $wfsConfig;

        if ($wfsConfig['shortart']) {
            if (!XOOPS_USE_MULTIBYTES) {
                if (mb_strlen($this->title) >= 19) {
                    $this->title = mb_substr($this->title, 0, 18) . '...';
                }
            }
        }

        $ret = "<a href='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/article.php?articleid=' . $this->articleid() . "'>" . $this->title($format) . '</a>';

        return $ret;
    }

    public function iconLink($format = 'Show')
    {
        global $xoopsModule;

        $ret = '';

        if ($this->getFilesCount() || !empty($this->maintext) || '1' == $this->ishtml) {
            if ($this->url || $this->ishtml) {
                $ret .= "<a href='" . $this->url() . "'><img align='absmiddle' src='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/images/icon/html.gif'> </a>";
            } else {
                $ret .= "<img align='absmiddle' src='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/images/icon/default.gif'> ";
            }

            $ret .= $this->textLink($format);

            return $ret;
        }

        return $this->title($format);
    }

    // HTML output

    public function preview($format = 'Show', $page = -1, $pageurl = '')
    {
        $myts = MyTextSanitizer::getInstance();

        global $xoopsDB, $xoopsConfig, $xoopsModule, $xoopsUser, $popular, $groupid, $wfsConfig;

        $datetime = formatTimestamp(time(), $wfsConfig['timestamp']);

        $counter = 0;

        global $xoopsUser, $xoopsConfig, $wfsConfig;

        if ($this->uid > 0) {
            $user = new xoopsUser($this->uid);

            if (($wfsConfig['realname']) && $user->getVar('name')) {
                $poster = $user->getVar('name');
            } else {
                $poster = $user->getVar('uname');
            }

            $poster = "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $this->uid() . "'>" . $poster . '</a>';
        } else {
            $poster = $GLOBALS['xoopsConfig']['anonymous'];
        }

        // $datetime

        if (isset($this->published)) {
            $datetime = formatTimestamp($this->published, (string)$wfsConfig[timestamp]);
        }

        // $title

        $title = $this->category->textLink() . ': ';

        $title .= $this->title();

        //Counter

        if (isset($this->counter)) {
            $counter = $this->counter;
        }

        $pagenum = $this->maintextPages() - 1;

        if ($page > $pagenum) {
            $page = $pagenum;
        }

        $maintext = '';

        if (-2 == $page) {
            $page = 0;
        }

        if ($this->maintextFlag) {
            $maintext .= $this->maintextWithFile('P', $page);
        } else {
            $maintext .= $this->maintextWithFile('S', $page);
        }

        // Setup URL link for article

        $urllink = '';

        if (($this->url) && (!$this->urlname)) {
            $urllink = "<a href='" . $this->url() . "' target='_blank'>Url Link: " . $this->url() . '</a><br>';
        }

        if ($this->urlname) {
            $urllink .= "<a href='" . $this->url() . "' target='_blank'>Url Link: " . $this->urlname() . '</a><br>';
        }

        //maintext for articles

        //$maintext = $this->maintext;

        //Downloads links

        $workdir = XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath'];

        $downloadlink = "<table width='100%' cellspacing='1' cellpadding='2'>";

        if (isset($this->articleid) && $this->getFilesCount() > 0) {
            $downloadlink .= '<tr><td >';

            if ('Show' == $format) {
                $downloadlink .= "<tr><td colspan='2' class='itemHead' align='left'><b>" . _WFS_DOWNLOADS . " $this->title</b></td></tr>";
            } else {
                $downloadlink .= "<tr><td colspan='2' class='bg3' align='left'><b>" . _WFS_DOWNLOADS . " $this->title</b></td></tr>";
            }

            foreach ($this->files as $file) {
                $filename = $file->getFileRealName();

                $mimetype = new mimetype();

                $icon = get_icon($workdir . '/' . $filename);

                $size = filesize(XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath'] . '/' . $filename);

                if (empty($size)) {
                    $size = '0';
                }

                $downloadlink .= "<tr><td valign ='middle' height='10' width='50%' class='even'><img src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/images/icon/' . $icon . " align='middle'> : " . $file->getLinkedName(XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/download.php?fileid=') . '';

                $downloadlink .= "<br><a href='brokenfile.php?lid=$file->fileid'><div  align = right><span class='comUserStat'></b>[" . _WFS_REPORTBROKEN . ']</span></div></a>';

                $downloadlink .= '</td>';

                $downloadlink .= "<td width='50%' class='even' align='left' valign='top'><b>" . _WFS_DESCRIPTION . ':</b><br>' . $file->getFiledescript('S') . '</td>';

                $downloadlink .= '</tr>';

                $downloadlink .= "<tr><td class='odd' align='right' width='50%'>";

                $downloadlink .= '' . _WFS_FILETYPE . '' . $mimetype->getType($workdir . '/' . $filename) . '';

                $downloadlink .= '</td>';

                $downloadlink .= "<td class='odd' align='right' width='50%'>";

                $downloadlink .= "<img src='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/images/counter.gif' border='0' alt='downloads' align='absmiddle'>";

                $downloadlink .= '&nbsp;' . $file->getCounter() . "&nbsp;&nbsp;<img src='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/images/size.gif' border='0' align='absmiddle' alt='" . _WFS_FILESIZE . "'>";

                $downloadlink .= '&nbsp;' . PrettySize($size) . '</a>';

                $downloadlink .= '</td></tr>';
            }

            $downloadlink .= '</td></tr>';

            $downloadlink .= '</table><br>';
        }

        $imglink = '';

        $adminlink = '&nbsp;';

        $pagelink = '';

        //Show page numbers if page > 0

        if (-1 != $page && $pagenum) {
            $pagelink .= 'Page: ';

            for ($i = 0; $i <= $pagenum; $i++) {
                if ($page == ($i)) {
                    $pagelink .= "<a href='" . $pageurl . ($i) . "'><span style='color:#ee0000;font-weight:bold;'>" . ($i + 1) . '</span></a>&nbsp;';
                } else {
                    $pagelink .= "<a href='" . $pageurl . ($i) . "'>" . ($i + 1) . '</a>&nbsp;';
                }
            }

            $title .= ' (' . ($page + 1) . '/' . ($pagenum + 1) . ')';
        }

        if ($xoopsUser && 'Show' == $format) {
            if ($xoopsUser->isAdmin($xoopsModule->mid())) {
                $adminlink = " [ <a href='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() .
                        '/admin/index.php?op=edit&amp;articleid=' . $this->articleid . "'>" . _EDIT .
                        "</a> | <a href='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() .
                        '/admin/index.php?op=delete&amp;articleid=' . $this->articleid . "'>" . _DELETE . '</a> ] ';
            }
        }

        $maillink = "<a href='print.php?articleid=" . $this->articleid . "'><img src='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/images/print.gif' alt='" . _WFS_PRINTERFRIENDLY . "'></a> ";

        $maillink .= "<a target='_top' href='mailto:?subject=" . rawurlencode(sprintf(_WFS_INTFILEAT, $xoopsConfig['sitename'])) . '&body=' . rawurlencode(sprintf(_WFS_INTFILEFOUND, $xoopsConfig['sitename']) . ':  ' . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/index.php?articleid=' . $this->articleid) . "'><img src='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/images/friend.gif' alt='" . _WFS_TELLAFRIEND . "'></a>";

        $ratethisfile = "<a href='ratefile.php?lid=" . $this->articleid . "'>" . _WFS_RATETHISFILE . '</a>';

        $catlink = "<a href='./index.php?category=" . $this->categoryid() . "'>" . _WFS_BACK2CAT . "</a><b> | </b><a href='./index.php'>" . _WFS_RETURN2INDEX . '</a>';

        $rating = '<b>' . sprintf(_WFS_RATINGA, number_format($this->rating, 2)) . '</b>';

        $votes = '<b>(' . sprintf(_WFS_NUMVOTES, $this->votes) . ')</b>';

        $fullcount = format_size(mb_strlen($maintext));

        if ('1' == $this->ishtml && $this->htmlpage()) {
            $maintext = XOOPS_ROOT_PATH . '/' . $wfsConfig['htmlpath'] . '/' . $this->htmlpage;

            $fullcount = prettysize(filesize($maintext));
        }

        echo "<table width='100%' border='0' cellspacing='1' cellpadding='2' class = 'outer'>";

        echo "<tr class='bg3' >";

        echo "<td ><span class='itemTitle' align = 'left'>" . $title . '</b>';

        echo '' . $adminlink . '</span></td>';

        echo '</tr>';

        echo '<tr>';

        echo "<td valign='top' class='head' colspan='2'>";

        echo "<table width='100%' border='0' cellspacing='0' cellpadding='0'>";

        echo "<tr><td width=84% class= 'itemPoster'  >";

        echo '' . _WFS_AUTHER . " $poster <br>";

        echo '' . _WFS_PUBLISHEDHOME . ": $datetime <br>";

        echo '' . sprintf(_WFS_VIEWS, $counter) . '<br>';

        echo '' . sprintf(_WFS_ARTSIZE, $fullcount) . '';

        echo '</td>';

        echo "<td width='16%' align='right' valign='middle'>$maillink</td>";

        echo '</tr>';

        echo '</table>';

        echo '</td>';

        echo '</tr>';

        echo '<tr><td>';

        if ($urllink) {
            echo $urllink . '<br>';
        } else {
            echo '&nbsp';
        }

        echo '</td></tr>';

        echo '<tr><td>';

        if ('1' == $this->ishtml && $this->htmlpage()) {
            include $maintext;
        } else {
            echo $maintext . '</b>';
        }

        echo '</td></tr>';

        echo '<tr><td>';

        echo '&nbsp';

        echo '</td></tr>';

        echo '<tr><td>';

        if ($pagelink) {
            echo $pagelink;
        }

        echo '</td></tr>';

        echo '</table>';
    }

    //Start of edit page for articles, more work needed I think!!!!

    public function editform()
    {
        global $xoopsModule, $HTTP_SERVER_VARS, $_POST, $groupid, $myts, $xoopsConfig, $xoopsUser, $xoopsDB, $textareaname, $wfsConfig;

        require XOOPS_ROOT_PATH . '/include/xoopscodes.php';

        $textareaname = '';

        //$maintext = '';

        echo "<table width='100%' border='0' cellspacing='0' cellpadding='1'>";

        echo "<table><tr><td><form action='index.php' method='post' name='coolsus'>";

        echo '<div><b>' . _AM_GROUPPROMPT . '</b><br>';

        if (isset($this->groupid)) {
            listGroups($this->groupid);
        } else {
            listGroups();
        }

        echo '<br>';

        echo '</div><br>';

        echo '<div><b>' . _WFS_CATEGORY . '</b><br>';

        $xt = new WfsCategory();

        if (isset($this->categoryid)) {
            $xt->makeSelBox(0, $this->categoryid, 'categoryid');
        } else {
            $xt->makeSelBox(0, 0, 'categoryid');
        }

        echo '</div><br>';

        echo '<div><b>' . _AM_ARTICLEWEIGHT . '</b><br>';

        echo "<input type='text' name='weight' id='weight' value='";

        if (isset($this->weight)) {
            echo $this->weight('F');
        } else {
            $this->weight = 0;

            echo $this->weight('F');
        }

        echo "' size='5'></div><br>";

        echo '<div>' . _WFS_CAUTH . '<br></div>';

        echo "<div><select name='changeuser'>";

        echo "<option value='-1'>------</option>";

        $result = $xoopsDB->query('SELECT uid, uname FROM ' . $xoopsDB->prefix('users') . ' ORDER BY uname');

        while (list($uid, $uname) = $xoopsDB->fetchRow($result)) {
            if ($uid == $this->uid) {
                $opt_selected = "selected='selected'";
            } else {
                $opt_selected = '';
            }

            echo "<option value='" . $uid . "' $opt_selected>" . $uname . '</option>';
        }

        echo '</select></div><br>';

        echo '<div><b>' . _WFS_TITLE . '</b><br>';

        echo "<input type='text' name='title' id='title' value='";

        if (isset($this->title)) {
            if ($this->titleFlag) {
                echo $this->title('F');
            } else {
                echo $this->title('E');
            }
        }

        echo "' size='50'></div><br>";

        //HTML Page Seclection//

        echo '<div><b>' . _WFS_HTMLPAGE . '</b></div>';

        //echo " <b>HTML Path: </b>".$htmlpath."<br><br></div>";

        $html_array = XoopsLists::getFileListAsArray(XOOPS_ROOT_PATH . '/' . $wfsConfig['htmlpath']);

        echo "<div><select size='1' name='htmlpage'>";

        echo "<option value=' '>------</option>";

        foreach ($html_array as $htmlpage) {
            if ($htmlpage == $this->htmlpage()) {
                $opt_selected = "selected='selected'";
            } else {
                $opt_selected = '';
            }

            echo "<option value='" . $htmlpage . "' $opt_selected>" . $htmlpage . '</option>';
        }

        echo '</select>';

        $htmlpath = XOOPS_ROOT_PATH . '/' . $wfsConfig['htmlpath'];

        echo ' <b>HTML Path: </b>' . $htmlpath . '<br><br></div>';

        //echo "</div><br>";

        echo '<div><b>' . _WFS_MAINTEXT . '</b></div>';

        if (isset($this->maintext)) {
            if ($this->maintextFlag) {
                $GLOBALS['maintext'] = $this->maintext('F');
            } else {
                $GLOBALS['maintext'] = $this->maintext('E');
            }
        }

        if (!mb_strstr($HTTP_SERVER_VARS['HTTP_USER_AGENT'], 'MSIE')) {
            $wfsConfig['wysiwygeditor'] = '0';
        }

        if ('1' == $wfsConfig['wysiwygeditor']) {
            html_editor('maintext');

            $smiliepath = $wfsConfig['smiliepath'];

            $smilie_array = XoopsLists::getImgListAsArray(XOOPS_ROOT_PATH . '/' . $smiliepath);

            echo "<br><div style='text-align: left;'><b>" . _AM_SMILIE . '</b><br>';

            echo "<table><tr><td align='top' valign='left'>";

            echo "<div><script type='text/javascript'>
		<!--
			function showbutton() {
			   	document.all." . $textareaname . "_mysmile.src = '" . $xoopsConfig['xoops_url'] . "/$smiliepath/' + document.all." . $textareaname . '_smiley.value;
			}
		// -->
		</script>';

            echo "<select name='" . $textareaname . "_smiley' onchange='showbutton();'>";

            foreach ($smilie_array as $file) {
                echo "<option value='" . $file . "' $opt_selected>" . $file . '</option>';
            }

            echo "</select></td><td align='top' valign='left'>";

            echo "<img name='" . $textareaname . "_mysmile' src='" . $xoopsConfig['xoops_url'] . "/$smiliepath/$file' style='cursor:hand;' border='0' onclick=\"doFormat('InsertImage', document.all." . $textareaname . '_mysmile.src);">';

            echo "</td></tr></table>
		<script type='text/javascript'>
			showbutton();
		</script>";

            //Start of article images

            $graphpath = $wfsConfig['graphicspath'];

            $graph_array = XoopsLists::getImgListAsArray(XOOPS_ROOT_PATH . '/' . $graphpath);

            echo "<br><div style='text-align: left;'><b>" . _AM_GRAPHIC . '</b><br>';

            echo "<table><tr><td align='top' valign='left'>";

            echo "<script type='text/javascript'>
		<!--
			function showbutton2() {
				document.all." . $textareaname . "_mygraph.src = '" . $xoopsConfig['xoops_url'] . "/$graphpath/' + document.all." . $textareaname . '_graph.value;
			}
		// -->
		</script>';

            echo "<select name='" . $textareaname . "_graph' onchange='showbutton2();'>";

            foreach ($graph_array as $file2) {
                echo "<option value='" . $file2 . "' $opt_selected>" . $file2 . '</option>';
            }

            echo "</select></td><td align='top' valign='left'>";

            echo "<img name='" . $textareaname . "_mygraph' src='" . $xoopsConfig['xoops_url'] . "/$graphpath/$file2' style='cursor:hand;' border='0' onclick=\"doFormat('InsertImage', document.all." . $textareaname . '_mygraph.src);">';

            echo "</td></tr></table>
		<script type='text/javascript'>
			showbutton2();
		</script>";
        } else {
            xoopsCodeTarea('maintext', 60, 15);

            xoopsSmilies('maintext');
        }

        echo '<div><b>' . _WFS_SUMMARY . '</b></div>';

        echo "<div><textarea id='summary' name='summary' wrap='virtual' cols='60' rows='5'>";

        if (isset($this->summary)) {
            if ($this->summaryFlag) {
                echo $this->summary('F');
            } else {
                echo $this->summary('E');
            }
        }

        echo '</textarea></div>';

        echo "<div class = 'bg3'><h4>" . _WFS_ARTICLELINK . '</h4></div>';

        echo '<div><b>' . _WFS_LINKURL . '</b><br>';

        echo "<input type='text' name='url' id='url' value='";

        if (isset($this->url)) {
            echo $this->url('F');
        }

        echo "' size='70'></div><br>";

        echo '<div><b>' . _WFS_LINKURLNAME . '</b><br>';

        echo "<input type='text' name='urlname' id='urlname' value='";

        if (isset($this->urlname)) {
            echo $this->urlname('F');
        }

        echo "' size='50'></div><br>";

        echo "<div class = 'bg3'><h4>" . _WFS_ATTACHEDFILES . '</h4></div>';

        echo '<div>' . _WFS_ATTACHEDFILESTXT . '</div><br>';

        if (empty($this->articleid)) {
            echo _WFS_AFTERREGED . '<br>';
        } elseif ($num = $this->getFilesCount()) {
            echo "<table border='1' style='border-collapse: collapse' bordercolor='#ffffff' width='100%' >";

            echo "<tr class='bg3'><td align='center'>" . _AM_FILEID . "</td><td align='center'>" . _AM_FILEICON . "</td><td align='center'>" . _AM_FILESTORE . "</td><td align='center'>" . _AM_REALFILENAME . "</td><td align='center'>" . _AM_USERFILENAME . "</td><td align='center' class='nw'>" . _AM_FILEMIMETYPE . "</td><td align='center' class='nw'>" . _AM_FILESIZE . "</td><td align='center'>" . _AM_ACTION . '</td></tr>';

            foreach ($this->files as $attached) {
                if (is_file(XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath'] . '/' . $attached->getFileRealName())) {
                    $filename = $attached->getFileRealName();
                } else {
                    $filename = 'File Error!';
                }

                $fileid = $attached->getFileid();

                $mimetype = new mimetype();

                $icon = get_icon(XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath'] . '/' . $filename);

                $iconshow = '<img src=' . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/images/icon/' . $icon . " align='middle'>";

                if (is_file(XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath'] . '/' . $filename)) {
                    $size = Prettysize(filesize(XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath'] . '/' . $filename));
                } else {
                    $size = '0';
                }

                $filerealname = $attached->downloadname;

                $mimeshow = $mimetype->getType(XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath'] . '/' . $filename);

                $counter = $attached->getCounter();

                $linkedname = $attached->getFileShowName();

                //$linkedname = $attached->getLinkedName(XOOPS_URL."/modules/".$xoopsModule->dirname()."/download.php?fileid=");

                $editlink = "<a href='index.php?op=fileedit&amp;fileid=" . $fileid . "'>" . _AM_EDIT . '</a>';

                $dellink = "<a href='index.php?op=delfile&amp;fileid=" . $fileid . "'>" . _AM_DELETE . '</a>';

                echo "<tr><td align='center'><b>" . $fileid . '</b>';

                echo "</td><td align='center'>" . $iconshow . '';

                echo "</td><td align='center'>" . $filename . '';

                echo "</td><td align='center'>" . $filerealname . '';

                echo "</td><td align='center'>" . $linkedname . '';

                echo "</td><td align='center'>" . $mimeshow . '';

                echo "</td><td align='center'>" . $size . '';

                //echo "</td><td align='center' class='nw'>".$counter."";

                echo "</td><td align='center'>" . $editlink . ' ' . $dellink . '';

                echo '</td></tr>';
            }

            echo '</table>';
        } else {
            echo "<div align='left'>" . _WFS_NOFILE . '</div>';
        }

        echo '</div><br>';

        echo "<div class = 'bg3'><h4>" . _WFS_MISCSETTINGS . '</h4></div>';

        echo "<input type='checkbox' name='autodate' value='1'";

        if (isset($autodate) && 1 == $autodate) {
            echo ' checked';
        }

        echo '> ';

        $time = time();

        if (!empty($this->articleid)) {
            $isedit = 1;
        }

        if (isset($isedit) && 1 == $isedit && $this->published > $time) {
            echo '<b>' . _AM_CHANGEDATETIME . '</b><br><br>';

            printf(_AM_NOWSETTIME, formatTimestamp($this->published));

            $published = xoops_getUserTimestamp($this->published);

            echo '<br><br>';

            printf(_AM_CURRENTTIME, formatTimestamp($time));

            echo '<br>';

            echo "<input type='hidden' name='isedit' value='1'>";
        } else {
            echo '<b>' . _AM_SETDATETIME . '</b><br><br>';

            printf(_AM_CURRENTTIME, formatTimestamp($time));

            echo '<br>';
        }

        echo '<br> &nbsp; ' . _AM_MONTHC . " <select name='automonth'>";

        if (isset($automonth)) {
            $automonth = (int)$automonth;
        } elseif (isset($this->published)) {
            $automonth = date('m', $this->published);
        } else {
            $automonth = date('m');
        }

        for ($xmonth = 1; $xmonth < 13; $xmonth++) {
            if ($xmonth == $automonth) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }

            echo "<option value='$xmonth' $sel>$xmonth</option>";
        }

        echo '</select>&nbsp;';

        echo _AM_DAYC . " <select name='autoday'>";

        if (isset($autoday)) {
            $autoday = (int)$autoday;
        } elseif (isset($published)) {
            $autoday = date('d', $this->published);
        } else {
            $autoday = date('d');
        }

        for ($xday = 1; $xday < 32; $xday++) {
            if ($xday == $autoday) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }

            echo "<option value='$xday' $sel>$xday</option>";
        }

        echo '</select>&nbsp;';

        echo _AM_YEARC . " <select name='autoyear'>";

        if (isset($autoyear)) {
            $autoyear = (int)$autoyear;
        } elseif (isset($this->published)) {
            $autoyear = date('Y', $this->published);
        } else {
            $autoyear = date('Y');
        }

        $cyear = date('Y');

        for ($xyear = ($autoyear - 8); $xyear < ($cyear + 2); $xyear++) {
            if ($xyear == $autoyear) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }

            echo "<option value='$xyear' $sel>$xyear</option>";
        }

        echo '</select>';

        echo '&nbsp;' . _AM_TIMEC . " <select name='autohour'>";

        if (isset($autohour)) {
            $autohour = (int)$autohour;
        } elseif (isset($this->publishedshed)) {
            $autohour = date('H', $this->published);
        } else {
            $autohour = date('H');
        }

        for ($xhour = 0; $xhour < 24; $xhour++) {
            if ($xhour == $autohour) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }

            echo "<option value='$xhour' $sel>$xhour</option>";
        }

        echo '</select>';

        echo " : <select name='automin'>";

        if (isset($automin)) {
            $automin = (int)$automin;
        } elseif (isset($published)) {
            $automin = date('i', $published);
        } else {
            $automin = date('i');
        }

        for ($xmin = 0; $xmin < 61; $xmin++) {
            if ($xmin == $automin) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }

            $xxmin = $xmin;

            if ($xxmin < 10) {
                $xxmin = (string)$xmin;
            }

            echo "<option value='$xmin' $sel>$xxmin</option>";
        }

        echo '</select></br>';

        echo "<br><input type='checkbox' name='autoexpdate' value='1'";

        if (isset($autoexpdate) && 1 == $autoexpdate) {
            echo ' checked';
        }

        echo '> ';

        $time = time();

        if (isset($isedit) && 1 == $isedit && $this->expired > 0) {
            echo '<b>' . _AM_CHANGEEXPDATETIME . '</b><br><br>';

            printf(_AM_NOWSETEXPTIME, formatTimestamp($this->expired));

            echo '<br><br>';

            $expired = xoops_getUserTimestamp($this->expired);

            printf(_AM_CURRENTTIME, formatTimestamp($time));

            echo '<br>';

            echo "<input type='hidden' name='isedit' value='1'>";
        } else {
            echo '<b>' . _AM_SETEXPDATETIME . '</b><br><br>';

            printf(_AM_CURRENTTIME, formatTimestamp($time));

            echo '<br>';
        }

        echo '<br> &nbsp; ' . _AM_MONTHC . " <select name='autoexpmonth'>";

        if (isset($autoexpmonth)) {
            $autoexpmonth = (int)$autoexpmonth;
        } elseif (isset($expired)) {
            $autoexpmonth = date('m', $expired);
        } else {
            $autoexpmonth = date('m');

            $autoexpmonth += 1;
        }

        for ($xmonth = 1; $xmonth < 13; $xmonth++) {
            if ($xmonth == $autoexpmonth) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }

            echo "<option value='$xmonth' $sel>$xmonth</option>";
        }

        echo '</select>&nbsp;';

        echo _AM_DAYC . " <select name='autoexpday'>";

        if (isset($autoexpday)) {
            $autoexpday = (int)$autoexpday;
        } elseif (isset($expired)) {
            $autoexpday = date('d', $expired);
        } else {
            $autoexpday = date('d');
        }

        for ($xday = 1; $xday < 32; $xday++) {
            if ($xday == $autoexpday) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }

            echo "<option value='$xday' $sel>$xday</option>";
        }

        echo '</select>&nbsp;';

        echo _AM_YEARC . " <select name='autoexpyear'>";

        if (isset($autoexpyear)) {
            $autoyear = (int)$autoexpyear;
        } elseif (isset($expired)) {
            $autoexpyear = date('Y', $expired);
        } else {
            $autoexpyear = date('Y');
        }

        $cyear = date('Y');

        for ($xyear = ($autoexpyear - 8); $xyear < ($cyear + 2); $xyear++) {
            if ($xyear == $autoexpyear) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }

            echo "<option value='$xyear' $sel>$xyear</option>";
        }

        echo '</select>';

        echo '&nbsp;' . _AM_TIMEC . " <select name='autoexphour'>";

        if (isset($autoexphour)) {
            $autoexphour = (int)$autoexphour;
        } elseif (isset($expired)) {
            $autoexphour = date('H', $expired);
        } else {
            $autoexphour = date('H');
        }

        for ($xhour = 0; $xhour < 24; $xhour++) {
            if ($xhour == $autoexphour) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }

            echo "<option value='$xhour' $sel>$xhour</option>";
        }

        echo '</select>';

        echo " : <select name='autoexpmin'>";

        if (isset($autoexpmin)) {
            $autoexpmin = (int)$autoexpmin;
        } elseif (isset($expired)) {
            $autoexpmin = date('i', $expired);
        } else {
            $autoexpmin = date('i');
        }

        for ($xmin = 0; $xmin < 61; $xmin++) {
            if ($xmin == $autoexpmin) {
                $sel = 'selected="selected"';
            } else {
                $sel = '';
            }

            $xxmin = $xmin;

            if ($xxmin < 10) {
                $xxmin = "0$xmin";
            }

            echo "<option value='$xmin' $sel>$xxmin</option>";
        }

        echo '</select><br><br>';

        if (isset($this->published) && 0 == $this->published && isset($this->type) && 'user' == $this->type) {
            echo "<div><input type='checkbox' name='approved' value='1' checked>&nbsp;<b>" . _AM_APPROVE . '</b></div><br>';
        }

        echo "<br><div><input type='checkbox' name='nosmiley' value='1'";

        if (isset($this->nosmiley) && 1 == $this->nosmiley) {
            echo ' checked';
        }

        echo '> <b>' . _WFS_DISAMILEY . '</b></div>';

        echo "<div><input type='checkbox' name='nohtml' value='1'";

        if (isset($this->nohtml) && 1 == $this->nohtml) {
            echo ' checked';
        }

        echo '> <b>' . _WFS_DISHTML . '</b><br>';

        echo '</div><br>';

        if (isset($isedit) && 1 == $isedit) {
            echo "<input type='checkbox' name='movetotop' value='1'";

            if (isset($movetotop) && 1 == $movetotop) {
                echo ' checked';
            }

            echo '>&nbsp;<b>' . _AM_MOVETOTOP . '</b><br>';
        }

        echo "<br><div><input type='checkbox' name='justhtml' value='2'";

        if (isset($this->htmlpage) && '2' == $this->ishtml) {
            echo ' checked';
        }

        echo '>' . _AM_JUSTHTML . '<br></div>';

        echo "<div><input type='checkbox' name='noshowart' value='1'";

        if (isset($this->noshowart) && 1 == $this->noshowart) {
            echo ' checked';
        }

        echo '> ' . _AM_NOSHOART . '<br>';

        echo '</div><br>';

        echo "<input type='checkbox' name='offline' value='1'";

        if (isset($this->offline) && 1 == $this->offline) {
            echo ' checked';
        }

        echo '>&nbsp;' . _AM_OFFLINE . '<br>';

        echo '<br>';

        // added ladon spotlight

        echo "<input type='checkbox' name='spotlight' value='1'";

        if (isset($this->spotlight) && 1 == $this->spotlight) {
            echo ' checked';
        }

        echo '>&nbsp;' . _AM_ISSPOTLIGHT . '<br>';

        echo '<br>';

        if (!empty($this->articleid)) {
            echo "<input type='hidden' name='articleid' value='" . $this->articleid . "'>\n";
        }

        if (!empty($_POST['referer'])) {
            echo "<input type='hidden' name='referer' value='" . $_POST['referer'] . "'>\n";
        } elseif (!empty($HTTP_SERVER_VARS['HTTP_REFERER'])) {
            echo "<input type='hidden' name='referer' value='" . $HTTP_SERVER_VARS['HTTP_REFERER'] . "'>\n";
        }

        echo "<input type='submit' name='op' class='formButton' value='Preview'>&nbsp;<input type='submit' name='op' class='formButton' value='Save'>&nbsp;<input type='submit' name='op' class='formButton' value='Clean'>";

        echo '</form>';

        echo '</td></tr></table>';

        if (!empty($this->articleid)) {
            echo '<hr>';

            $upload = new UploadFile();

            echo $upload->formStart('index.php?op=fileup');

            echo '<h4>' . _WFS_FILEUPLOAD . "</h4>\n";

            echo '' . _WFS_ATTACHFILEACCESS . '<br>';

            echo '<br><b>' . _WFS_ATTACHFILE . '</b><br>';

            echo $upload->formMax();

            echo $upload->formField();

            echo '<br><br><b>' . _WFS_FILESHOWNAME . '</b><br>';

            echo "<input type='text' name='fileshowname' id='fileshowname' value='";

            if (isset($this->fileshowname)) {
                echo $this->fileshowname;
            }

            echo "' size='70' maxlength='80'><br>";

            echo '<br><b>' . _WFS_FILEDESCRIPT . '</b><br>';

            echo "<textarea name='textfiledescript' cols='50' rows='5'></textarea><br>";

            echo '<br><b>' . _WFS_FILETEXT . '</b><br>';

            echo "<textarea name='textfilesearch' cols='50' rows='3'></textarea><br>";

            echo "<input type='hidden' name='groupid' value='" . $this->groupip . "'>";

            echo "<input type='hidden' name='articleid' value='" . $this->articleid . "'>";

            echo "<input type='hidden' name='groupid' value= '" . $this->groupid . "'>";

            echo $upload->formSubmit(_WFS_UPLOAD);

            echo $upload->formEnd();
        }
    }

    public function loadPostVars()
    {
        global $_POST, $myts, $xoopsUser, $xoopsConfig;

        $this->groupid = saveAccess($_POST['groupid']);

        $this->setTitle($_POST['title']);

        $this->setMaintext($_POST['maintext']);

        $this->setCategoryid($_POST['categoryid']);

        $htmlpage = $myts->stripSlashesGPC($_POST['htmlpage']);

        $this->setChangeuser($_POST['changeuser']);

        $this->setHtmlpage($_POST['htmlpage']);

        $this->setWeight($_POST['weight']);

        if (!empty($_POST['autodate'])) {
            $pubdate = mktime($_POST['autohour'], $_POST['automin'], 0, $_POST['automonth'], $_POST['autoday'], $_POST['autoyear']);

            $offset = $xoopsUser->timezone() - $xoopsConfig['server_TZ'];

            $pubdate -= ($offset * 3600);

            $this->setPublished($pubdate);
        }

        if (!empty($_POST['autoexpdate'])) {
            $expdate = mktime($_POST['autoexphour'], $_POST['autoexpmin'], 0, $_POST['autoexpmonth'], $_POST['autoexpday'], $_POST['autoexpyear']);

            $offset = $xoopsUser->timezone() - $xoopsConfig['server_TZ'];

            $expdate -= ($offset * 3600);

            $this->setExpired($expdate);
        } else {
            $this->setExpired(0);
        }

        if (!empty($_POST['movetotop'])) {
            $this->setPublished(time());
        }

        $this->noshowart = (isset($_POST['noshowart'])) ? 1 : 0;

        $this->nohtml = (isset($_POST['nohtml'])) ? 1 : 0;

        $this->nosmiley = (isset($HTTP_POST_VAR['nosmiley'])) ? 1 : 0;

        $this->approved = (isset($_POST['approved'])) ? 1 : 0;

        $this->offline = (isset($_POST['offline'])) ? 1 : 0;

        $this->notifypub = (isset($_POST['notifypub'])) ? 1 : 0;

        $this->ishtml = (isset($_POST['htmlpage']) && ' ' != $_POST['htmlpage']) ? 1 : 0;

        $this->spotlight = (isset($_POST['spotlight'])) ? 1 : 0;

        if (2 == $_POST['justhtml']) {
            $this->ishtml = 2;
        }

        if (isset($_POST['summary'])) {
            $this->setSummary($_POST['summary']);
        }

        if (isset($_POST['url'])) {
            $this->setUrl($_POST['url']);
        }

        if (isset($_POST['urlname'])) {
            $this->setUrlname($_POST['urlname']);
        }

        if (isset($_POST['page'])) {
            $this->setPage($_POST['page']);
        }
    }
}
