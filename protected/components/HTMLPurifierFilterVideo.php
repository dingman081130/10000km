<?php
class HTMLPurifierFilterVideo extends HTMLPurifier_Filter
{

    public $name = 'Video';

    public function preFilter($html, $config, $context) {
        $pre_regex = '/<embed src="([a-zA-Z0-9\.\/:_\-&\?=;]+)"[^>]+>(<\/embed>)?/m';
        $pre_replace = '#<span class="youku-embed">$1</span>#';
        return preg_replace($pre_regex, $pre_replace, $html);
    }

    public function postFilter($html, $config, $context) {
        $post_regex = '/#<span class="youku-embed">([a-zA-Z0-9\.\/:_\-&\?=;]+)<\/span>#/m';
        return preg_replace_callback($post_regex, array($this, 'postFilterCallback'), $html);
    }

    protected function postFilterCallback($matches) {
        $url = $matches[1];
        file_put_contents('/tmp/sb.txt', $url);
        return '<embed src="'. $url .'" allowFullScreen="true" quality="high" width="480" height="400" align="middle" allowScriptAccess="always" type="application/x-shockwave-flash"></embed>';
    }
}
