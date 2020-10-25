<?php
// Class for Category management for WfSection
// $Id: wfscategory.php,v 1.4 Date: 06/01/2003, Author: Catzwolf Exp $

require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
require_once XOOPS_ROOT_PATH . '/modules/zmagazine/include/functions.php';
require_once XOOPS_ROOT_PATH . '/modules/zmagazine/class/wfstree.php';

class WfsCategory
{
    public $db;

    public $table;

    public $id;

    public $pid;

    public $title;

    public $imgurl;

    public $displayimg;

    public $description;

    public $catdescription;

    public $catfooter;

    public $articles;

    public $newest_time;

    public $newest_uid;

    public $groupid;

    public $editaccess;

    public $orders;

    // constructor

    public function __construct($catid = 0)
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();

        $this->table = $this->db->prefix('zmag_category');

        if (is_array($catid)) {
            $this->makeCategory($catid);
        } elseif (0 != $catid) {
            $this->loadCategory($catid);
        } else {
            $this->id = $catid;
        }
    }

    // set

    public function setTitle($value)
    {
        $this->title = $value;
    }

    public function setImgurl($value)
    {
        $this->imgurl = $value;
    }

    public function setDisplayimg($value)
    {
        $this->displayimg = $value;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setcatdescription($catdescription)
    {
        $this->catdescription = $catdescription;
    }

    public function setcatfooter($catfooter)
    {
        $this->catfooter = $catfooter;
    }

    public function setPid($value)
    {
        $this->pid = $value;
    }

    public function setOrders($value)
    {
        $this->orders = $value;
    }

    public function setgroupid($value)
    {
        $this->groupid = saveaccess($value);
    }

    public function seteditaccess($value)
    {
        $this->editaccess = saveaccess($value);
    }

    // database

    public function loadCategory($id)
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id=' . $id . '';

        $array = $this->db->fetchArray($this->db->query($sql));

        $this->makeCategory($array);
    }

    public function makeCategory($array)
    {
        foreach ($array as $key => $value) {
            $this->$key = $value;
        }
    }

    public function store()
    {
        global $myts;

        $myts = MyTextSanitizer::getInstance();

        $title = '';

        $imgurl = '';

        $description = '';

        $catdescription = '';

        $catfooter = '';

        if (isset($this->title) && '' != $this->title) {
            $title = $myts->addSlashes($this->title);
        }

        if (isset($this->imgurl) && '' != $this->imgurl) {
            $imgurl = $myts->addSlashes($this->imgurl);
        }

        if (isset($this->displayimg) && '' != $this->displayimg) {
            $displayimg = $myts->addSlashes($this->displayimg);
        }

        if (isset($this->description) && '' != $this->description) {
            $description = $myts->addSlashes($this->description);
        }

        if (isset($this->catdescription) && '' != $this->catdescription) {
            $catdescription = $myts->addSlashes($this->catdescription);
        }

        if (isset($this->catfooter) && '' != $this->catfooter) {
            $catfooter = $myts->addSlashes($this->catfooter);
        }

        if (!isset($this->pid) || !is_numeric($this->pid)) {
            $this->pid = 0;
        }

        if (empty($this->id)) {
            $this->id = $this->db->genId($this->table . '_id_seq');

            $sql = 'INSERT INTO ' . $this->table . ' (id, pid, imgurl, displayimg, title, description, catdescription, groupid, catfooter, orders, editaccess) VALUES (' . $this->id . ', ' . $this->pid . ", '" . $imgurl . "', " . $displayimg . ", '" . $title . "', '" . $description . "', '" . $catdescription . "','" . $this->groupid . "', '" . $catfooter . "', " . $this->orders . ",'" . $this->editaccess . "')";
        } else {
            $sql = 'UPDATE ' . $this->table . ' SET pid=' . $this->pid . ", imgurl='" . $imgurl . "', displayimg=" . $displayimg . ", title='" . $title . "', description='" . $description . "', catdescription='" . $catdescription . "', groupid='" . $this->groupid . "', catfooter='" . $catfooter . "', orders='" . $this->orders . "', editaccess='" . $this->editaccess . "' WHERE id=" . $this->id . ' ';
        }

        if (!$result = $this->db->query($sql)) {
            ErrorHandler::show('0022');
        }

        return true;
    }

    public function delete()
    {
        $sql = 'DELETE FROM ' . $this->table . ' WHERE id=' . $this->id . '';

        $this->db->query($sql);
    }

    // get

    public function id()
    {
        return $this->id;
    }

    public function pid()
    {
        return $this->pid;
    }

    public function title($format = 'S')
    {
        if (!isset($this->title)) {
            return '';
        }

        $myts = MyTextSanitizer::getInstance();

        switch ($format) {
                case 'S':
                    $title = htmlspecialchars($this->title, ENT_QUOTES | ENT_HTML5);
                    break;
                case 'E':
                    $title = htmlspecialchars($this->title, ENT_QUOTES | ENT_HTML5);
                    break;
                case 'P':
                    $title = htmlspecialchars($this->title, ENT_QUOTES | ENT_HTML5);
                    break;
                case 'F':
                    $title = htmlspecialchars($this->title, ENT_QUOTES | ENT_HTML5);
                    break;
            }

        return $title;
    }

    public function imgurl($format = 'S')
    {
        $myts = MyTextSanitizer::getInstance();

        switch ($format) {
                case 'S':
                    $imgurl = htmlspecialchars($this->imgurl, ENT_QUOTES | ENT_HTML5);
                    break;
                case 'E':
                    $imgurl = htmlspecialchars($this->imgurl, ENT_QUOTES | ENT_HTML5);
                    break;
                case 'P':
                    $imgurl = htmlspecialchars($this->imgurl, ENT_QUOTES | ENT_HTML5);
                    break;
                case 'F':
                    $imgurl = htmlspecialchars($this->imgurl, ENT_QUOTES | ENT_HTML5);
                    break;
            }

        return $imgurl;
    }

    public function description($format = 'S')
    {
        if (!isset($this->description)) {
            return '';
        }

        $html = 1;

        $smiley = 1;

        $xcodes = 1;

        $myts = MyTextSanitizer::getInstance();

        switch ($format) {
                case 'S':
                $description = $myts->displayTarea($this->description, $html, $smiley, $xcodes);
                break;
                case 'E':
                $description = htmlspecialchars($this->description, ENT_QUOTES | ENT_HTML5);
                break;
                case 'P':
                $description = $myts->previewTarea($this->description, $html, $smiley, $xcodes);
                break;
                case 'F':
                $description = htmlspecialchars($this->description, ENT_QUOTES | ENT_HTML5);
                break;
            }

        return $description;
    }

    public function catdescription($format = 'S')
    {
        if (!isset($this->catdescription)) {
            return '';
        }

        $html = 1;

        $smiley = 1;

        $xcodes = 1;

        $myts = MyTextSanitizer::getInstance();

        switch ($format) {
                case 'S':
                    $catdescription = $myts->displayTarea($this->catdescription, $html, $smiley, $xcodes);
                    break;
                case 'E':
                    $catdescription = htmlspecialchars($this->catdescription, ENT_QUOTES | ENT_HTML5);
                    break;
                case 'P':
                    $catdescription = $myts->previewTarea($this->catdescription, $html, $smiley, $xcodes);
                    break;
                case 'F':
                    $catdecription = htmlspecialchars($this->catdescription, ENT_QUOTES | ENT_HTML5);
                    break;
                }

        return $catdescription;
    }

    public function catfooter($format = 'S')
    {
        if (!isset($this->catfooter)) {
            return '';
        }

        $html = 1;

        $smiley = 1;

        $xcodes = 1;

        $myts = MyTextSanitizer::getInstance();

        switch ($format) {
                case 'S':
                    $catfooter = $myts->displayTarea($this->catfooter, $html, $smiley, $xcodes);
                    break;
                case 'E':
                    $catfooter = htmlspecialchars($this->catfooter, ENT_QUOTES | ENT_HTML5);
                    break;
                case 'P':
                    $catfooter = $myts->previewTarea($this->catfooter, $html, $smiley, $xcodes);
                    break;
                case 'F':
                    $catfooter = htmlspecialchars($this->catfooter, ENT_QUOTES | ENT_HTML5);
                    break;
            }

        return $catfooter;
    }

    public function orders()
    {
        return $this->orders;
    }

    public function getFirstChild()
    {
        $ret = [];

        $xt = new XoopsTree($this->table, 'id', 'pid');

        $category_arr = $xt->getFirstChild($this->id, 'orders');

        if (is_array($category_arr) && count($category_arr)) {
            foreach ($category_arr as $category) {
                $ret[] = new self($category);
            }
        }

        return $ret;
    }

    public function getAllChild()
    {
        $ret = [];

        $xt = new XoopsTree($this->table, 'id', 'pid');

        $category_arr = $xt->getAllChild($this->id, orders);

        if (is_array($category_arr) && count($category_arr)) {
            foreach ($category_arr as $category) {
                $ret[] = new self($category);
            }
        }

        return $ret;
    }

    public function getChildTreeArray()
    {
        $ret = [];

        $xt = new XoopsTree($this->table, 'id', 'pid');

        $category_arr = $xt->getChildTreeArray($this->id, 'orders');

        if (is_array($category_arr) && count($category_arr)) {
            foreach ($category_arr as $category) {
                $ret[] = new self($category);
            }
        }

        return $ret;
    }

    public function getAllChildId($sel_id = 0, $order = '', $parray = [])
    {
        //$db = XoopsDatabaseFactory::getDatabaseConnection();

        $sql = 'SELECT id FROM ' . $this->table . ' WHERE pid=' . $sel_id . '';

        if ('' != $order) {
            $sql .= " ORDER BY $order";
        }

        $result = $this->db->query($sql);

        $count = $this->db->getRowsNum($result);

        if (0 == $count) {
            return $parray;
        }

        while ($row = $this->db->fetchArray($result)) {
            $parray[] = $row['id'];

            $parray = $this->getAllChildId($row['id'], $order, $parray);
        }

        return $parray;
    }

    public function isInChild($sel_id)
    {
        if (empty($this->id)) {
            return false;
        }

        if ($sel_id == $this->id) {
            return true;
        }

        $child = $this->getAllChildId();

        if (in_array($sel_id, $child, true)) {
            return true;
        }

        return false;
    }

    // public - WfsCategory::* style

    public function countByArticle($articleid = 0)
    {
        $db = XoopsDatabaseFactory::getDatabaseConnection();

        $sql = 'SELECT COUNT(*) FROM ' . $db->prefix('zmag_category') . '';

        if (0 != $articleid) {
            $sql .= " WHERE articleid=$articleid";
        }

        $result = $db->query($sql);

        [$count] = $db->fetchRow($result);

        return $count;
    }

    // HTML output

    public function makeSelBox($none = 0, $selcategory = -1, $selname = '', $onchange = '')
    {
        $xt = new wfsTree($this->table, 'id', 'pid');

        if (-1 != $selcategory) {
            $xt->makeMySelBox('title', 'title', $selcategory, $none, $selname, $onchange);
        } elseif (!empty($this->id)) {
            $xt->makeMySelBox('title', 'title', $this->id, $none, $selname, $onchange);
        } else {
            $xt->makeMySelBox('title', 'title', 0, $none, $selname, $onchange);
        }
    }

    // HTML string

    //generates nicely formatted linked path from the root id to a given id

    public function getNicePathFromId($funcURL)
    {
        $xt = new XoopsTree($this->table, 'id', 'pid');

        $ret = $xt->getNicePathFromId($this->id, 'title', $funcURL);

        return $ret;
    }

    public function getNicePathToPid($funcURL)
    {
        if (0 != $this->pid()) {
            $xt = new self($this->pid());

            $ret = $xt->getNicePathToPid($funcURL) .
                            " >> <a href='" . $funcURL . $this->pid() . "'>" . $xt->title() . '</a>';

            return $ret;
        }
  

        return '';
    }

    /*	function imgLink(){
                    global $xoopsModule;

                    $ret = "<a href='".XOOPS_URL."/modules/".$xoopsModule->dirname()."/index.php?category=".$this->id()."'>".
                    "<img src='".XOOPS_URL."/modules/".$xoopsModule->dirname()."/images/topics/".$this->imgurl().
                    "' alt='".$this->title()."'></a>";
                    return $ret;
            }*/

    public function textLink()
    {
        global $xoopsModule;

        $ret = "<a href='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() .
                '/index.php?category=' . $this->id() . "'>" .
                $this->title() . '</a>';

        return $ret;
    }
}
