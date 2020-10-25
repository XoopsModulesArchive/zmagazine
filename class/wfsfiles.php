<?php
// $Id: wfsfiles.php,v 1.4 Date: 06/01/2003, Author: Catzwolf Exp $
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/groupaccess.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/class/mimetype.php';
require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';

class WfsFiles
{
    public $db;

    public $table;

    public $fileid;

    public $articleid;

    public $filerealname;

    public $fileshowname;

    public $filetext;

    public $filedescript;

    public $date;

    public $ext;

    public $minetype;

    public $downloadname;

    public $counter;

    public $groupid;

    // constructor

    public function __construct($fileid = -1)
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();

        $this->table = $this->db->prefix('zmag_files');

        $this->articleid = 0;

        $this->filerealname = '';

        $this->fileshowname = '';

        $this->filetext = '';

        $this->filedescript = '';

        $this->date = '';

        $this->ext = '';

        $this->minetype = '';

        $this->downloadname = 'downloadfile';

        $this->counter = 0;

        if (is_array($fileid)) {
            $this->makeFile($fileid);
        } elseif (-1 != $fileid) {
            $this->getFile($fileid);
        }
    }

    // set

    public function setFileRealName($filename)
    {
        // TODO: check $filename exists

        $this->filerealname = $filename;
    }

    public function setFileShowName($filename)
    {
        $this->fileshowname = $filename;
    }

    public function setArticleid($id)
    {
        // TODO: check Article $id exists

        $this->articleid = $id;
    }

    public function setFiletext($text)
    {
        $this->filetext = $text;
    }

    public function setFiledescript($descript)
    {
        $this->filedescript = $descript;
    }

    public function setMinetype($value)
    {
        $this->minetype = $value;
    }

    public function setExt($value)
    {
        $this->ext = $value;
    }

    public function setDownloadname($value)
    {
        $this->downloadname = $value;
    }

    public function setgroupid($value)
    {
        $this->groupid = saveaccess($value);
    }

    public function setByUploadFile($uploadfile)
    {
        //  $uploadfile = uploadfile class instance

        global  $xoopsModule, $wfsConfig;

        $workdir = XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath'];

        $filename = $uploadfile->getFileName();

        $reg = '/^' . implode("\/", explode('/', XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath'])) . "\//";

        $this->filerealname = preg_replace($reg, '', $filename);

        $this->ext = $uploadfile->getExt();

        $this->minetype = $uploadfile->getMinetype();

        $this->downloadname = $uploadfile->getOriginalName();

        $this->setFiletextByFile();
    }

    public function setFiletextByFile()
    {
        global $WfsHelperDir, $xoopsModule, $xoopsConfig, $wfsConfig;

        $workdir = XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath'];

        // set fullpath of $this->filerealname

        if (preg_match("/^\/|~[ABCDEFGHIJKLMNOPQRSTQVWXYZ]:\//", $this->filerealname)) {
            $filename = $this->filerealname;
        } else {
            $filename = $this->filerealname;
        }

        // helper app & character set convertor

        if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/language/' . $xoopsConfig['language'] . '/convert.php')) {
            $langdir = XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/language/' . $xoopsConfig['language'];
        } else {
            $langdir = XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/language/english';
        }

        require_once $langdir . '/convert.php';

        switch ($this->minetype) {
                        case 'text/plain':
                                $this->filetext = implode(' ', file($filename));
                                $this->filetext = WfsConvert::TextPlane($this->filetext);
                                break;
                        case 'text/html':
                                $this->filetext = implode(' ', file($filename));
                                //echo "text/html<br>";
                                $this->filetext = WfsConvert::TextHtml($this->filetext);
                                break;
                        case 'application/vnd.ms-excel':
                                if (!empty($WfsHelperDir['application/vnd.ms-excel'])) {
                                    exec(XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/helper/' . $WfsHelperDir['application/vnd.ms-excel'] . '/xlhtml -te ' .
                                                $filename, $ret);

                                    $this->filetext = implode(' ', $ret);

                                    $this->filetext = WfsConvert::TextHtml($this->filetext);

                                    //echo "filetext = ".$this->filetext."<br>";
                                }
                                break;
                        case 'application/pdf':
                                if (!empty($WfsHelperDir['application/pdf'])) {
                                    $distfile = tempnam($workdir . '/temp/', 'pdf');

                                    exec(XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/helper/' . $WfsHelperDir['application/pdf'] . '/pdftotext ' .
                                        '-cfg ' . $langdir . '/xpdfrc ' .
                                        $filename . ' ' . $distfile);

                                    $this->filetext = implode(' ', file($distfile));

                                    $this->filetext = WfsConvert::stripSpaces($this->filetext);

                                    unlink($distfile);
                                }
                                break;
                        case 'default':
                        default:
                        $this->filetext = '';
                }
    }

    // get

    public function getFileShowName($format = 'S')
    {
        $myts = MyTextSanitizer::getInstance();

        $smiley = 1;

        switch ($format) {
                        case 'S':
                        case 'Show':
                                $fileShowName = htmlspecialchars($this->fileshowname, $smiley);
                                break;
                        case 'E':
                        case 'Edit':
                                $fileShowName = htmlspecialchars($this->fileshowname, ENT_QUOTES | ENT_HTML5);
                                break;
                        case 'P':
                        case 'Preview':
                                $fileShowNamee = htmlspecialchars($this->fileshowname, $smiley);
                                break;
                        case 'F':
                        case 'InForm':
                                $fileShowName = htmlspecialchars($this->fileshowname, ENT_QUOTES | ENT_HTML5);
                                break;
                }

        return $fileShowName;
    }

    public function getFileRealName($format = 'S')
    {
        $myts = MyTextSanitizer::getInstance();

        $smiley = 0;

        switch ($format) {
                        case 'S':
                        case 'Show':
                                $filerealname = htmlspecialchars($this->filerealname, $smiley);
                                break;
                        case 'E':
                        case 'Edit':
                                $filerealname = htmlspecialchars($this->filerealname, ENT_QUOTES | ENT_HTML5);
                                break;
                        case 'P':
                        case 'Preview':
                                $filerealname = htmlspecialchars($this->filerealname, $smiley);
                                break;
                        case 'F':
                        case 'InForm':
                                $filerealname = htmlspecialchars($this->filerealname, ENT_QUOTES | ENT_HTML5);
                                break;
                }

        return $filerealname;
    }

    public function getExt($format = 'S')
    {
        $myts = MyTextSanitizer::getInstance();

        $smiley = 0;

        switch ($format) {
                        case 'S':
                        case 'Show':
                                $fileext = htmlspecialchars($this->ext, $smiley);
                                break;
                        case 'E':
                        case 'Edit':
                                $fileext = htmlspecialchars($this->ext, ENT_QUOTES | ENT_HTML5);
                                break;
                        case 'P':
                        case 'Preview':
                                $fileext = htmlspecialchars($this->ext, $smiley);
                                break;
                        case 'F':
                        case 'InForm':
                                $fileext = htmlspecialchars($this->ext, ENT_QUOTES | ENT_HTML5);
                                break;
                }

        return $fileext;
    }

    public function getMinetype($format = 'S')
    {
        $myts = MyTextSanitizer::getInstance();

        $smiley = 0;

        switch ($format) {
                        case 'S':
                        case 'Show':
                                $filemimetype = htmlspecialchars($this->minetype, $smiley);
                                break;
                        case 'E':
                        case 'Edit':
                                $filemimetype = htmlspecialchars($this->minetype, ENT_QUOTES | ENT_HTML5);
                                break;
                        case 'P':
                        case 'Preview':
                                $filemimetype = htmlspecialchars($this->minetype, $smiley);
                                break;
                        case 'F':
                        case 'InForm':
                                $filemimetype = htmlspecialchars($this->minetype, ENT_QUOTES | ENT_HTML5);
                                break;
                }

        return $filemimetype;
    }

    public function getFileText($format = 'S')
    {
        $myts = MyTextSanitizer::getInstance();

        $smiley = 0;

        switch ($format) {
                        case 'S':
                        case 'Show':
                                $filetext = $myts->displayTarea($this->filetext, $smiley);
                                break;
                        case 'E':
                        case 'Edit':
                                $filetext = htmlspecialchars($this->filetext, ENT_QUOTES | ENT_HTML5);
                                break;
                        case 'P':
                        case 'Preview':
                                $filetext = $myts->previewTarea($this->filetext, $smiley);
                                break;
                        case 'F':
                        case 'InForm':
                                $filetext = htmlspecialchars($this->filetext, ENT_QUOTES | ENT_HTML5);
                                break;
                }

        return $filetext;
    }

    public function getFiledescript($format = 'S')
    {
        $myts = MyTextSanitizer::getInstance();

        $html = 1;

        $smiley = 1;

        $xcodes = 1;

        switch ($format) {
                        case 'S':
                        case 'Show':
                                $filedescript = $myts->displayTarea($this->filedescript, $html, $smiley, $xcodes);
                                break;
                        case 'E':
                        case 'Edit':
                                $filedescript = htmlspecialchars($this->filedescript, ENT_QUOTES | ENT_HTML5);
                                break;
                        case 'P':
                        case 'Preview':
                                $filedescript = $myts->previewTarea($this->filedescript, $smiley);
                                break;
                        case 'F':
                        case 'InForm':
                                $filedescript = htmlspecialchars($this->filedescript, ENT_QUOTES | ENT_HTML5);
                                break;
                }

        return $filedescript;
    }

    public function getDownloadname($format = 'S')
    {
        $myts = MyTextSanitizer::getInstance();

        $smiley = 0;

        switch ($format) {
                        case 'S':
                        case 'Show':
                                $filedownname = htmlspecialchars($this->downloadname, $smiley);
                                break;
                        case 'E':
                        case 'Edit':
                                $filedownname = htmlspecialchars($this->downloadname, ENT_QUOTES | ENT_HTML5);
                                break;
                        case 'P':
                        case 'Preview':
                                $filedownname = htmlspecialchars($this->downloadname, $smiley);
                                break;
                        case 'F':
                        case 'InForm':
                                $filedownname = htmlspecialchars($this->downloadname, ENT_QUOTES | ENT_HTML5);
                                break;
                }

        return $filedownname;
    }

    public function getFileid()
    {
        return $this->fileid;
    }

    public function getLinkedName($funcURL)
    {
        $myts = MyTextSanitizer::getInstance();

        return "<a href='" . $funcURL . $this->fileid . "'>" . htmlspecialchars($this->fileshowname, ENT_QUOTES | ENT_HTML5) . '</a>';
    }

    public function getArticleid()
    {
        return $this->articleid;
    }

    public function getCounter()
    {
        return $this->counter;
    }

    // public - WfsArticle::* style

    public function getAllbyArticle($articleid)
    {
        $db = XoopsDatabaseFactory::getDatabaseConnection();

        $table = $db->prefix('zmag_files');

        $ret = [];

        $sql = 'SELECT * FROM ' . $table . ' WHERE articleid=' . $articleid . '';

        $result = $db->query($sql);

        while ($myrow = $db->fetchArray($result)) {
            $ret[] = new self($myrow);
        }

        return $ret;
    }

    // database

    public function getFile($id)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE fileid=' . $id . '';

        $array = $this->db->fetchArray($this->db->query($sql));

        $this->makeFile($array);
    }

    public function makeFile($array)
    {
        foreach ($array as $key => $value) {
            $this->$key = $value;
        }
    }

    public function store()
    {
        $myts = MyTextSanitizer::getInstance();

        $fileRealName = $myts->addSlashes($this->filerealname);

        $fileShowName = $myts->censorString($this->fileshowname);

        $fileShowName = $myts->addSlashes($fileShowName);

        $filetext = $myts->addSlashes($this->filetext);

        $filedescript = $myts->addSlashes($this->filedescript);

        $downloadname = $myts->addSlashes($this->downloadname);

        $groupid = saveaccess($this->groupid);

        $date = time();

        $ext = $myts->addSlashes($this->ext);

        $minetype = $myts->addSlashes($this->minetype);

        $counter = (int)$this->counter;

        $articleid = (int)$this->articleid;

        if (!isset($this->fileid)) {
            $newid = $this->db->genId($this->table . '_fileid_seq');

            $sql = 'INSERT INTO ' . $this->table .
                        ' (fileid, articleid, filerealname, fileshowname, filetext, filedescript, date, ext, minetype, downloadname, counter, groupid) ' .
                        'VALUES (' . $newid . ',' . $articleid . ",'" . $fileRealName . "','" . $fileShowName .
                        "','" . $filetext . "','" . $filedescript . "'," . $date . ",'" . $ext . "','" . $minetype . "','" . $downloadname . "'," . $counter . ",'" . $groupid . "')";
        } else {
            $sql = 'UPDATE ' . $this->table .
                        ' SET articleid=' . $articleid . ",filerealname='" . $this->filerealname .
                        "',fileshowname='" . $fileShowName . "',filetext='" . $filetext . "', filedescript='" . $filedescript . "',date=" . $date .
                        ",ext='" . $ext . "',minetype='" . $minetype . "',downloadname='" . $downloadname . "', groupid='" . $groupid . "',counter=" . $counter .
                        ' WHERE fileid=' . $this->fileid . '';
        }

        if (!$result = $this->db->query($sql)) {
            return false;
        }

        return true;
    }

    public function delete()
    {
        global $WfsHelperDir,$xoopsModule,$xoopsConfig, $wfsConfig;

        $sql = 'DELETE FROM ' . $this->table . ' WHERE fileid=' . $this->fileid . '';

        if (!$result = $this->db->query($sql)) {
            return false;
        }

        $workdir = XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath'];

        if (file_exists($workdir . '/' . $this->filerealname)) {
            unlink($workdir . '/' . $this->filerealname);
        }

        return true;
    }

    public function updateCounter()
    {
        $sql = 'UPDATE ' . $this->table . ' SET counter=counter+1 WHERE fileid=' . $this->fileid . '';

        if (!$result = $this->db->queryF($sql)) {
            return false;
        }

        return true;
    }

    // HTML output

    public function editform()
    {
        global $xoopsModule, $wfsConfig, $xoopsConfig;

        require XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        $mimetype = new mimetype();

        xoops_cp_header();

        $article = new WfsArticle($this->articleid);

        $atitle = "<a href='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/article.php?articleid=' . $this->articleid . "'>" . $article->title . '</a>';

        $stform = new XoopsThemeForm(_AM_FILESTATS, 'op', xoops_getenv('PHP_SELF'));

        echo '<div><h3>' . _AM_FILEATTACHED . '</h3></div>';

        $stform->addElement(new XoopsFormLabel(_AM_FILESTAT, $atitle));

        $stform->addElement(new XoopsFormLabel(_WFS_FILEID, 'No: ' . $this->fileid));

        $workdir = XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath'];

        if (file_exists(realpath($workdir . '/' . $this->filerealname))) {
            $error = 'File <b>' . $this->filerealname . '</b> exists on server.';
        } else {
            $error = 'ERROR, File <b>' . $this->filerealname . '</b> please check!';
        }

        $stform->addElement(new XoopsFormLabel(_WFS_ERRORCHECK, $error));

        $stform->addElement(new XoopsFormLabel(_WFS_FILEREALNAME, $this->getFileRealName('F')));

        $stform->addElement(new XoopsFormLabel(_WFS_DOWNLOADNAME, $this->getDownloadname('F')));

        $stform->addElement(new XoopsFormLabel(_WFS_MINETYPE, $this->getMinetype('F')));

        $stform->addElement(new XoopsFormLabel(_WFS_EXT, '.' . $this->getExt('F')));

        $stform->addElement(new XoopsFormLabel(_WFS_FILEPERMISSION, get_perms(XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath'] . '/' . $this->getFileRealName('F'))));

        $stform->addElement(new XoopsFormLabel(_WFS_DOWNLOADED, $this->getCounter('F') . ' times'));

        $stform->addElement(new XoopsFormLabel(_WFS_DOWNLOADSIZE, PrettySize(filesize(XOOPS_ROOT_PATH . '/' . $wfsConfig['filesbasepath'] . '/' . $this->getFileRealName('F')))));

        $stform->addElement(new XoopsFormLabel(_WFS_LASTACCESS, lastaccess($workdir . '/' . $this->filerealname, 'E1')));

        $stform->addElement(new XoopsFormLabel(_WFS_LASTUPDATED, formatTimestamp($this->date, $wfsConfig['timestamp'])));

        //$stform->addElement(new XoopsFormLabel(_WFS_FILEREALNAME, $this->getFileRealName("F")));

        $stform->display();

        clearstatcache();

        $sform = new XoopsThemeForm(_AM_MODIFYFILE, 'op', xoops_getenv('PHP_SELF'));

        echo '<div><h3>' . _AM_EDITFILE . '</h3></div>';

        //global $xoopsConfig, $xoopsDB, $_POST, $myts, $wfsConfig, $myts;

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        $sform = new XoopsThemeForm(_AM_MENUS, 'op', xoops_getenv('PHP_SELF'));

        $sform->addElement(new XoopsFormSelectGroup(_WFS_GROUPPROMPT, 'groupid', true, getGroupIda($this->groupid), 5, true));

        $sform->addElement(new XoopsFormLabel(_WFS_FILEID, 'No: ' . $this->fileid));

        $sform->addElement(new XoopsFormText(_WFS_ARTICLEID, 'articleid', 5, 5, $this->articleid));

        $sform->addElement(new XoopsFormText(_WFS_FILEREALNAME, 'filerealname', 40, 40, $this->getFileRealName('F')));

        $sform->addElement(new XoopsFormText(_WFS_DOWNLOADNAME, 'downloadname', 40, 40, $this->getDownloadname('F')));

        $sform->addElement(new XoopsFormText(_WFS_FILESHOWNAME, 'fileshowname', 40, 80, $this->getFileShowName('F')));

        $sform->addElement(new XoopsFormDhtmlTextArea(_WFS_FILEDESCRIPT, 'filedescript', $this->getFiledescript('F'), 10, 60));

        $sform->addElement(new XoopsFormTextArea(_WFS_FILETEXT, 'filetext', $this->getFileText('F')));

        $sform->addElement(new XoopsFormText(_WFS_EXT, 'ext', 30, 80, $this->getExt('F')));

        $sform->addElement(new XoopsFormText(_WFS_MINETYPE, 'minetype', 40, 80, $this->getMinetype('F')));

        $sform->addElement(new XoopsFormLabel(_WFS_UPDATEDATE, formatTimestamp($this->date, $wfsConfig['timestamp'])));

        $sform->addElement(new XoopsFormHidden('fileid', $this->fileid));

        //echo $this->fileid;

        //echo "<input type='hidden' name='fileid' value='$this->fileid'>\n";

        ///$sform->addElement(new XoopsFormHidden('fileid', ".$this->fileid."));

        $button_tray = new XoopsFormElementTray('', '');

        //$hidden = new XoopsFormHidden('fileid', $this->fileid);

        $hidden = new XoopsFormHidden('op', 'filesave');

        $button_tray->addElement($hidden);

        $button_tray->addElement(new XoopsFormButton('', 'post', _AM_SAVECHANGE, 'submit'));

        $sform->addElement($button_tray);

        $sform->display();

        unset($hidden);
    }

    public function loadPostVars()
    {
        global $_POST, $myts, $xoopsUser, $xoopsConfig;

        $this->setFileRealName($_POST['filerealname']);

        $this->setFileShowName($_POST['fileshowname']);

        $this->setArticleid($_POST['articleid']);

        $this->setFiletext($_POST['filetext']);

        $this->setFiledescript($_POST['filedescript']);

        $this->setMinetype($_POST['minetype']);

        $this->setExt($_POST['ext']);

        $this->setDownloadname($_POST['downloadname']);

        $this->setgroupid($_POST['groupid']);
    }
}
