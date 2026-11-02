<?php
/** News Detail summary widget with configurable social sharing. */
if (!defined('ABSPATH')) exit;

class PDS_Detail_Summary_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct('pds_detail_summary', 'PDS Detail - Editable Post Summary', array('description'=>'Edit cover, post metadata and social sharing.'));
    }
    private function defaults() {
        return array(
            'image_id'=>0,'image_url'=>'','title'=>'','date'=>'','author'=>'','category'=>'',
            'share_label'=>'SHARE','back_label'=>'BACK TO NEWS','back_url'=>'',
            'show_date'=>1,'show_author'=>1,'show_category'=>1,'show_share'=>1,'show_back'=>1,
            'social_facebook'=>1,'social_x'=>1,'social_linkedin'=>1,'social_whatsapp'=>1,
            'social_telegram'=>1,'social_pinterest'=>1,'social_copy'=>1,
        );
    }
    public function widget($args,$instance) {
        if (!is_singular('post')) return;
        $v=wp_parse_args((array)$instance,$this->defaults());
        $id=get_queried_object_id();
        $cats=get_the_category($id);
        $news_page=get_page_by_path('news');
        $news_url=$news_page?get_permalink($news_page):home_url('/news/');
        $title=$v['title']!==''?$v['title']:get_the_title($id);
        $date=$v['date']!==''?$v['date']:get_the_date('M d, Y',$id);
        $author=$v['author']!==''?$v['author']:get_the_author_meta('display_name',(int)get_post_field('post_author',$id));
        $category=$v['category']!==''?$v['category']:implode(', ',array_slice(wp_list_pluck($cats,'name'),0,2));
        $back_url=$v['back_url']!==''?$v['back_url']:$news_url;
        $url=get_permalink($id);
        $encoded_url=rawurlencode($url);
        $encoded_title=rawurlencode($title);
        $share_links=array(
            'social_facebook'=>array('Facebook','https://www.facebook.com/sharer/sharer.php?u='.$encoded_url),
            'social_x'=>array('X (Twitter)','https://twitter.com/intent/tweet?url='.$encoded_url.'&text='.$encoded_title),
            'social_linkedin'=>array('LinkedIn','https://www.linkedin.com/sharing/share-offsite/?url='.$encoded_url),
            'social_whatsapp'=>array('WhatsApp','https://api.whatsapp.com/send?text='.$encoded_title.'%20'.$encoded_url),
            'social_telegram'=>array('Telegram','https://t.me/share/url?url='.$encoded_url.'&text='.$encoded_title),
            'social_pinterest'=>array('Pinterest','https://pinterest.com/pin/create/button/?url='.$encoded_url.'&description='.$encoded_title),
        );
        $social_icons = array(
            'social_facebook' => 'fa-facebook-f',
            'social_x' => 'fa-x-twitter',
            'social_linkedin' => 'fa-linkedin-in',
            'social_whatsapp' => 'fa-whatsapp',
            'social_telegram' => 'fa-telegram',
            'social_pinterest' => 'fa-pinterest-p',
        );
        echo $args['before_widget'];
        echo '<header class="pds-news-detail-summary">';
        echo '<div class="pds-news-detail-cover">';
        if (absint($v['image_id'])) echo wp_get_attachment_image(absint($v['image_id']),'medium',false,array('alt'=>$title));
        elseif ($v['image_url']!=='') echo '<img src="'.esc_url($v['image_url']).'" alt="'.esc_attr($title).'">';
        elseif (has_post_thumbnail($id)) echo get_the_post_thumbnail($id,'medium',array('alt'=>$title));
        else echo '<span class="pds-news-detail-placeholder">PDS NEWS</span>';
        echo '</div><div class="pds-news-detail-summary-text"><h1>'.esc_html($title).'</h1>';
        if (!empty($v['show_date'])) echo '<p class="pds-news-detail-date">Published: '.esc_html($date).'</p>';
        echo '<div class="pds-news-detail-tags">';
        if (!empty($v['show_author'])) echo '<span>'.esc_html($author).'</span>';
        if (!empty($v['show_category']) && $category!=='') echo '<span>'.esc_html($category).'</span>';
        echo '</div></div><div class="pds-news-detail-actions">';
        if (!empty($v['show_share'])) {
            $menu_id='pds-share-menu-'.$this->number;
            echo '<div class="pds-detail-share-wrap">';
            // Deliberately not using .pds-news-detail-share: legacy single-post.php binds a copy-only click handler to it.
            echo '<button type="button" class="pds-detail-share-trigger" aria-expanded="false" aria-controls="'.esc_attr($menu_id).'">'.esc_html($v['share_label']).'</button>';
            echo '<div class="pds-detail-share-menu" id="'.esc_attr($menu_id).'" hidden>';
            foreach ($share_links as $key=>$item) {
                if (empty($v[$key])) continue;
                echo '<a href="'.esc_url($item[1]).'" target="_blank" rel="noopener noreferrer" class="pds-detail-share-link pds-detail-share-'.esc_attr(str_replace('social_','',$key)).'"><i class="fa-brands '.esc_attr($social_icons[$key]).'" aria-hidden="true"></i><span>'.esc_html($item[0]).'</span></a>';
            }
            if (!empty($v['social_copy'])) echo '<button type="button" class="pds-detail-share-copy" data-url="'.esc_url($url).'"><i class="fa-solid fa-link" aria-hidden="true"></i><span>Copy Link</span></button>';
            echo '</div></div>';
        }
        if (!empty($v['show_back'])) echo '<a class="pds-news-detail-back" href="'.esc_url($back_url).'">'.esc_html($v['back_label']).'</a>';
        echo '</div></header>';
        echo $args['after_widget'];
    }
    public function form($instance) {
        $v=wp_parse_args((array)$instance,$this->defaults());
        $image_id=absint($v['image_id']);
        $preview=$image_id?wp_get_attachment_image_url($image_id,'medium'):'';
        if (!$preview && $v['image_url']) $preview=$v['image_url'];
        echo '<p><strong>Cover image</strong> (blank = current post featured image)</p>';
        echo '<div class="pds-summary-image-control">';
        foreach (array('image_id'=>'pds-summary-image-id','image_url'=>'pds-summary-image-url') as $key=>$class) {
            echo '<input type="hidden" class="'.esc_attr($class).'" name="'.esc_attr($this->get_field_name($key)).'" id="'.esc_attr($this->get_field_id($key)).'" value="'.esc_attr($v[$key]).'">';
        }
        echo '<div class="pds-summary-image-preview">';
        if ($preview) echo '<img src="'.esc_url($preview).'" alt="" style="max-width:100%;height:auto">';
        else echo '<em>Using featured image</em>';
        echo '</div><p><button type="button" class="button pds-summary-select-image">Choose image</button> <button type="button" class="button pds-summary-remove-image">Remove custom image</button></p></div>';
        $fields=array('title'=>'Post title (blank = automatic)','date'=>'Published date (blank = automatic)','author'=>'Author (blank = automatic)','category'=>'Category (blank = automatic)','share_label'=>'Share button text','back_label'=>'Back button text','back_url'=>'Back URL (blank = News page)');
        foreach ($fields as $key=>$label) {
            echo '<p><label for="'.esc_attr($this->get_field_id($key)).'">'.esc_html($label).'</label><input class="widefat" type="'.($key==='back_url'?'url':'text').'" id="'.esc_attr($this->get_field_id($key)).'" name="'.esc_attr($this->get_field_name($key)).'" value="'.esc_attr($v[$key]).'"></p>';
        }
        echo '<hr><p><strong>Display settings</strong></p>';
        $checks=array('show_date'=>'Show date','show_author'=>'Show author','show_category'=>'Show category','show_share'=>'Show SHARE button','show_back'=>'Show BACK TO NEWS','social_facebook'=>'Share on Facebook','social_x'=>'Share on X (Twitter)','social_linkedin'=>'Share on LinkedIn','social_whatsapp'=>'Share on WhatsApp','social_telegram'=>'Share on Telegram','social_pinterest'=>'Share on Pinterest','social_copy'=>'Show Copy Link');
        foreach ($checks as $key=>$label) {
            echo '<p><label><input type="checkbox" name="'.esc_attr($this->get_field_name($key)).'" value="1" '.checked(!empty($v[$key]),true,false).'> '.esc_html($label).'</label></p>';
        }
    }
    public function update($new,$old) {
        $v=$this->defaults();
        foreach (array('title','date','author','category','share_label','back_label') as $key) $v[$key]=sanitize_text_field($new[$key]??'');
        foreach (array('image_url','back_url') as $key) $v[$key]=esc_url_raw($new[$key]??'');
        $v['image_id']=absint($new['image_id']??0);
        foreach (array('show_date','show_author','show_category','show_share','show_back','social_facebook','social_x','social_linkedin','social_whatsapp','social_telegram','social_pinterest','social_copy') as $key) $v[$key]=empty($new[$key])?0:1;
        return $v;
    }
}
add_action('widgets_init',function(){register_widget('PDS_Detail_Summary_Widget');});

// Seed a default widget once; never overwrite an occupied area.
add_action('admin_init',function(){
    if (get_option('pds_detail_summary_seeded')) return;
    $areas=wp_get_sidebars_widgets();
    if (!empty($areas['pds-detail-summary'])) {update_option('pds_detail_summary_seeded',1);return;}
    $instances=get_option('widget_pds_detail_summary',array());
    $ids=array_filter(array_keys($instances),'is_numeric');
    $next=$ids?max(array_map('intval',$ids))+1:2;
    $instances[$next]=array('share_label'=>'SHARE','back_label'=>'BACK TO NEWS','show_date'=>1,'show_author'=>1,'show_category'=>1,'show_share'=>1,'show_back'=>1);
    update_option('widget_pds_detail_summary',$instances);
    $areas['pds-detail-summary']=array('pds_detail_summary-'.$next);
    wp_set_sidebars_widgets($areas);
    update_option('pds_detail_summary_seeded',1);
},30);

function pds_detail_social_assets(){
    wp_enqueue_style('pds-detail-social-icons','https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css',array(),'6.7.2');
    wp_enqueue_style('pds-detail-social',get_template_directory_uri().'/assets/css/pds-detail-social.css',array('pds-detail-social-icons'),'1.1.0');
    wp_enqueue_script('pds-detail-social',get_template_directory_uri().'/assets/js/pds-detail-social.js',array(),'1.0.0',true);
}
add_action('wp_enqueue_scripts',function(){if(is_singular('post'))pds_detail_social_assets();});

function pds_summary_media_assets(){
    wp_enqueue_media();
    wp_register_script('pds-summary-media',false,array('jquery'),'1.1.0',true);
    wp_enqueue_script('pds-summary-media');
    wp_add_inline_script('pds-summary-media',<<<'JS'
jQuery(function($){
    $(document).on('click','.pds-summary-select-image',function(e){
        e.preventDefault();
        const control=$(this).closest('.pds-summary-image-control');
        const frame=wp.media({title:'Choose News Detail cover',button:{text:'Use this image'},multiple:false,library:{type:'image'}});
        frame.on('select',function(){
            const item=frame.state().get('selection').first().toJSON();
            control.find('.pds-summary-image-id').val(item.id).trigger('change');
            control.find('.pds-summary-image-url').val('').trigger('change');
            const url=(item.sizes&&item.sizes.medium?item.sizes.medium.url:item.url);
            control.find('.pds-summary-image-preview').html($('<img>',{src:url,alt:''}).css({maxWidth:'100%',height:'auto'}));
        });
        frame.open();
    });
    $(document).on('click','.pds-summary-remove-image',function(e){
        e.preventDefault();
        const control=$(this).closest('.pds-summary-image-control');
        control.find('.pds-summary-image-id').val('0').trigger('change');
        control.find('.pds-summary-image-url').val('').trigger('change');
        control.find('.pds-summary-image-preview').html('<em>Using featured image</em>');
    });
});
JS
    );
}
add_action('customize_controls_enqueue_scripts','pds_summary_media_assets');
add_action('admin_enqueue_scripts',function($hook){if($hook==='widgets.php')pds_summary_media_assets();});
