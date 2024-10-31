<?php

/**
 * Inserts ads into posts
 * 
 * @since  2015-07-28
 * @author Patrick Forget <patforg@geekpad.ca>
 */
function oboxads_insert_ads_in_posts($content){
    if (is_single()) {
        $bannerCode = oboxadsGetAd('content');
        $bannerCode = str_replace(array("\r","\n") , "", $bannerCode);
        if(preg_match("/\[oboxads[^\]]*\]/",$content)){
            $content = preg_replace('/([oboxads[^\]]*\])/', $bannerCode . '$1', $content, 1);
        }else{
            $content = preg_replace('/(<p[^>]*>.*<\/p>)/', $bannerCode . '$1', $content, 1);
        } //if
    } //if
	
    return $content;
} // oboxads_insert_ads_in_posts()

/**
 * generate code for the [oboxads] shortcode
 * 
 * @since  2015-07-28
 * @author Patrick Forget <patforg@geekpad.ca>
 */
function oboxads_shortcode( $atts ){
    $bannerCode = '';
    if (is_single()) {
        extract( shortcode_atts( array(
            'type' => 'content',
        ), $atts ) );

        
        $bannerCode = oboxadsShowAd($type);
    } //if

	return $bannerCode;
} // oboxads_shortcode()
