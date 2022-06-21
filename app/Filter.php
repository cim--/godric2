<?php

namespace App;

class Filter
{

    public static function HTML($html)
    {
        // set configuration
        $config = \HTMLPurifier_Config::createDefault();
        // match tinymce elements allowed in editor config
        $config->set('HTML.Allowed', '*[class|title],a[href],p,ul,ol,li,em,strong,b,i,h2,h3,h4,h5,h6,table[summary],tr,td[abbr],th[abbr],thead,tbody,tfoot,br,img[src|alt|width|height],div,span');
        $config->set('HTML.Doctype', 'HTML 4.01 Strict');

        // run filter
        $purifier = new \HTMLPurifier($config);
        return $purifier->purify($html);
    }
}
