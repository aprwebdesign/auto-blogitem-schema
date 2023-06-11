<?php
/*
Plugin Name: Auto Blogitem Schema.org
Plugin URI: https://aprwebdesign.com
Description: Voegt automatisch schema.org markup toe aan blogartikelen.
Version: 1.0
Author: APR Webdesign
Author URI: https://aprwebdesign.com
*/

// Voeg schema.org markup toe aan de header van het blogartikel
function add_article_schema() {
    if (is_single()) {
        $post_id = get_queried_object_id();
        $article_markup = generate_article_markup($post_id);
        echo '<script type="application/ld+json">' . json_encode($article_markup) . '</script>';
    }
}
add_action('wp_head', 'add_article_schema');

// Genereer schema.org markup voor het blogartikel
function generate_article_markup($post_id) {
    $post = get_post($post_id);
    $categories = get_the_category($post_id);
    $category_names = array();
    foreach ($categories as $category) {
        $category_names[] = $category->name;
    }
    $author_id = $post->post_author;
    $author_url = get_author_posts_url($author_id);
    $markup = array(
        '@context' => 'http://schema.org',
        '@type' => 'BlogPosting',
        'mainEntityOfPage' => array(
            '@type' => 'WebPage',
            '@id' => get_permalink($post_id),
        ),
        'headline' => $post->post_title,
        'image' => get_the_post_thumbnail_url($post_id),
        'datePublished' => get_the_date('c', $post_id),
        'dateModified' => get_the_modified_date('c', $post_id),
        'author' => array(
            '@type' => 'Person',
            'name' => get_the_author_meta('display_name', $author_id),
            'url' => $author_url,
        ),
        'publisher' => array(
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'logo' => array(
                '@type' => 'ImageObject',
                'url' => get_template_directory_uri() . '/logo.png',
            ),
        ),
        'description' => get_the_excerpt($post_id),
        'wordCount' => str_word_count(strip_tags($post->post_content)),
    );

    return $markup;
}
