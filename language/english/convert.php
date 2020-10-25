<?php
// $Id: convert.php,v 1.2 2002/07/07 11:18:53 haruki Exp $

class WfsConvert
{
    public function TextPlane($text)
    {
        $text = preg_replace("/[\s\t\n]{2,}/", ' ', $text);

        return $text;
    }

    public function TextHtml($text)
    {
        $text = preg_replace("/[\s\t\n]{2,}/", ' ', $text);

        return $text;
    }

    public function stripSpaces($text)
    {
        $ret = preg_replace("/[\s\t\n]{2,}/", ' ', $text);

        return $ret;
    }

    public function filenameForWin($text)
    {
        return $text;
    }
}
