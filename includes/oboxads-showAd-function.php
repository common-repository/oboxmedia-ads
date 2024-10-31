<?php
/**
 * get banner code for given section
 *
 * @since  2015-07-31
 * @author Patrick Forget <patforg@geekpad.ca>
 */
function oboxadsGetAd($section, $options = array()) {
    static $validAttrs = array(
        'context' => true,
        'post-id' => true,
        'categories' => true,
        'tags' => true,
        'min-res' => true,
        'max-res' => true,
    );
    $escSection = esc_attr($section);

    $attrs = '';
    foreach ($options as $optionKey => $optionVal) {
        if (isset($validAttrs[$optionKey])) {
            $attrs .=  " data-{$optionKey}=\"". esc_attr($optionVal) ."\"";
        } //if
    } //foreach

    ob_start();
    echo <<<HTML
        <div class="oboxads" data-section="{$escSection}"{$attrs}>
            <div></div>
            <script>(OBOXADSQ || []).push({"cmd": "addBanner"});</script>
        </div>
HTML;
    $html = ob_get_contents();
    ob_end_clean();

    return $html;


} // oboxadsGetAd()

/**
 * Oboxmedia Wordpress Plugin Oboxads ShowAd Function
 * @version 1.0.0
 * @package Oboxmedia Wordpress Plugin
 */
function oboxadsShowAd ($section, $options = array()) {
    echo oboxadsGetAd($section, $options);
} //oboxadsShowAd()


