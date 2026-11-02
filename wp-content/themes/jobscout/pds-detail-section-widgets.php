<?php
/**
 * Dedicated News Detail widgets.
 * Replace pds-detail-section-widgets.php with this file's contents.
 * Requires the existing pds-detail-register-areas.php and summary widget.
 */
if (!defined('ABSPATH')) { exit; }

abstract class PDS_Detail_Block_Widget extends WP_Widget {
    protected $section = '';
    protected $fields = array();
    protected $initial = array();

    public function __construct() {
        parent::__construct('pds_detail_' . str_replace('-', '_', $this->section), 'PDS Detail - ' . $this->block_title(), array('description' => 'Edit only the settings for ' . $this->block_title() . '.'));
    }
    abstract protected function block_title();
    protected function defaults() { return array_merge(array('heading' => ''), $this->initial); }
    public function initial_instance() { return $this->defaults(); }
    protected function data($instance) { return wp_parse_args((array)$instance, $this->defaults()); }
    protected function field($key, $label, $type = 'text', $options = array()) {
        return array('key'=>$key,'label'=>$label,'type'=>$type,'options'=>$options);
    }
    public function form($instance) {
        $v = $this->data($instance);
        foreach ($this->fields as $field) {
            $key=$field['key']; $type=$field['type']; $label=$field['label'];
            $id=$this->get_field_id($key); $name=$this->get_field_name($key);
            echo '<p class="pds-detail-control">';
            if ($type==='checkbox') {
                echo '<label><input type="checkbox" name="'.esc_attr($name).'" id="'.esc_attr($id).'" value="1" '.checked(!empty($v[$key]),true,false).'> '.esc_html($label).'</label>';
            } elseif ($type==='textarea') {
                echo '<label for="'.esc_attr($id).'">'.esc_html($label).'</label><textarea class="widefat" rows="4" id="'.esc_attr($id).'" name="'.esc_attr($name).'">'.esc_textarea($v[$key]??'').'</textarea>';
            } elseif ($type==='select') {
                echo '<label for="'.esc_attr($id).'">'.esc_html($label).'</label><select class="widefat" id="'.esc_attr($id).'" name="'.esc_attr($name).'">';
                foreach ($field['options'] as $option=>$option_label) echo '<option value="'.esc_attr($option).'" '.selected($v[$key]??'', $option, false).'>'.esc_html($option_label).'</option>';
                echo '</select>';
            } elseif ($type==='image') {
                $image_id=absint($v[$key]??0);
                $src=$image_id?wp_get_attachment_image_url($image_id,'medium'):'';
                echo '<label>'.esc_html($label).'</label><div class="pds-detail-image-control"><input type="hidden" class="pds-detail-image-id" id="'.esc_attr($id).'" name="'.esc_attr($name).'" value="'.esc_attr($image_id).'">';
                echo '<div class="pds-detail-image-preview">'.($src?'<img src="'.esc_url($src).'" alt="" style="max-width:100%;height:auto">':'<em>Use the default image</em>').'</div>';
                echo '<button type="button" class="button pds-detail-choose-image">Choose image</button> <button type="button" class="button pds-detail-remove-image">Remove</button></div>';
            } else {
                $input_type=$type==='number'?'number':($type==='url'?'url':'text');
                $extra=$type==='number'?' min="1" max="12"':'';
                echo '<label for="'.esc_attr($id).'">'.esc_html($label).'</label><input class="widefat" type="'.$input_type.'" id="'.esc_attr($id).'" name="'.esc_attr($name).'" value="'.esc_attr($v[$key]??'').'"'.$extra.'>';
            }
            echo '</p>';
        }
    }
    public function update($new, $old) {
        $v=$this->defaults();
        foreach ($this->fields as $field) {
            $key=$field['key']; $type=$field['type']; $raw=$new[$key]??'';
            if ($type==='checkbox') $v[$key]=empty($raw)?0:1;
            elseif ($type==='number') $v[$key]=max(1,min(12,absint($raw)));
            elseif ($type==='image') $v[$key]=absint($raw);
            elseif ($type==='url') $v[$key]=esc_url_raw($raw);
            elseif ($type==='textarea') $v[$key]=wp_kses_post($raw);
            elseif ($type==='select') $v[$key]=isset($field['options'][$raw])?$raw:array_key_first($field['options']);
            else $v[$key]=sanitize_text_field($raw);
        }
        return $v;
    }
    protected function begin($args) { echo $args['before_widget']; }
    protected function end($args) { echo $args['after_widget']; }
    protected function post_id() { return get_queried_object_id(); }
    protected function news_url() { $p=get_page_by_path('news'); return $p?get_permalink($p):home_url('/news/'); }
    protected function heading($v,$fallback) { return esc_html($v['heading']!==''?$v['heading']:$fallback); }
    protected function image($id) { return $id?wp_get_attachment_image(absint($id),'large'):''; }
}

class PDS_Detail_Breadcrumb_Widget extends PDS_Detail_Block_Widget {
    protected $section='breadcrumb';
    protected $initial=array('home_label'=>'Home','news_label'=>'News','detail_label'=>'News Detail');
    protected function block_title(){return 'Breadcrumb';}
    public function __construct(){ $this->fields=array($this->field('home_label','Home label'),$this->field('news_label','News label'),$this->field('detail_label','Current page label'));parent::__construct(); }
    public function widget($args,$instance){if(!is_singular('post'))return;$v=$this->data($instance);$this->begin($args);echo '<nav aria-label="Breadcrumb"><a href="'.esc_url(home_url('/')).'">'.esc_html($v['home_label']).'</a> <span aria-hidden="true">/</span> <a href="'.esc_url($this->news_url()).'">'.esc_html($v['news_label']).'</a> <span aria-hidden="true">/</span> <span aria-current="page">'.esc_html($v['detail_label']).'</span></nav>';$this->end($args);}
}
class PDS_Detail_Header_Widget extends PDS_Detail_Block_Widget {
    protected $section='header';
    protected $initial=array('description'=>'','image_id'=>0);
    protected function block_title(){return 'Header Banner';}
    public function __construct(){$this->fields=array($this->field('heading','Banner heading'),$this->field('description','Description'),$this->field('image_id','Banner image','image'));parent::__construct();}
    public function widget($args,$instance){if(!is_singular('post'))return;$v=$this->data($instance);if(!$v['heading']&&!$v['description']&&!$v['image_id'])return;$this->begin($args);echo '<div class="pds-detail-custom-banner">'.$this->image($v['image_id']);if($v['heading'])echo '<h2>'.esc_html($v['heading']).'</h2>';if($v['description'])echo '<p>'.esc_html($v['description']).'</p>';echo '</div>';$this->end($args);}
}
class PDS_Detail_Article_Heading_Widget extends PDS_Detail_Block_Widget {
    protected $section='before-article';
    protected function block_title(){return 'Article Heading';}
    public function __construct(){$this->fields=array($this->field('heading','Section title (blank = Article Overview)'));parent::__construct();}
    public function widget($args,$instance){if(!is_singular('post'))return;$v=$this->data($instance);$this->begin($args);echo '<h2>'.$this->heading($v,'Article Overview').'</h2>';$this->end($args);}
}
class PDS_Detail_Article_Content_Widget extends PDS_Detail_Block_Widget {
    protected $section='article-content';
    protected $initial=array('show_content'=>1,'extra_html'=>'');
    protected function block_title(){return 'Article Content';}
    public function __construct(){$this->fields=array($this->field('show_content','Show current post content','checkbox'),$this->field('extra_html','Additional content (optional)','textarea'));parent::__construct();}
    public function widget($args,$instance){if(!is_singular('post'))return;$v=$this->data($instance);$this->begin($args);if(!empty($v['show_content'])){echo apply_filters('the_content',get_post_field('post_content',$this->post_id()));}echo wp_kses_post($v['extra_html']);$this->end($args);}
}
class PDS_Detail_After_Article_Widget extends PDS_Detail_Block_Widget {
    protected $section='after-article';
    protected $initial=array('extra_html'=>'','show_button'=>0,'button_label'=>'VIEW ALL NEWS','button_url'=>'');
    protected function block_title(){return 'After Article';}
    public function __construct(){$this->fields=array($this->field('extra_html','Additional content','textarea'),$this->field('show_button','Show button','checkbox'),$this->field('button_label','Button label'),$this->field('button_url','Button URL (blank = News)','url'));parent::__construct();}
    public function widget($args,$instance){if(!is_singular('post'))return;$v=$this->data($instance);if(!$v['extra_html']&&empty($v['show_button']))return;$this->begin($args);echo wp_kses_post($v['extra_html']);if($v['show_button'])echo '<a class="pds-news-read-more" href="'.esc_url($v['button_url']?:$this->news_url()).'">'.esc_html($v['button_label']).'</a>';$this->end($args);}
}
class PDS_Detail_Side_Intro_Widget extends PDS_Detail_Block_Widget {
    protected $section='sidebar';
    protected $initial=array('description'=>'','image_id'=>0);
    protected function block_title(){return 'Sidebar Introduction';}
    public function __construct(){$this->fields=array($this->field('heading','Title'),$this->field('description','Description'),$this->field('image_id','Image','image'));parent::__construct();}
    public function widget($args,$instance){if(!is_singular('post'))return;$v=$this->data($instance);if(!$v['heading']&&!$v['description']&&!$v['image_id'])return;$this->begin($args);echo '<section class="pds-news-detail-side-card">'.$this->image($v['image_id']);if($v['heading'])echo '<h2>'.esc_html($v['heading']).'</h2>';if($v['description'])echo '<p>'.esc_html($v['description']).'</p>';echo '</section>';$this->end($args);}
}
class PDS_Detail_Article_Info_Widget extends PDS_Detail_Block_Widget {
    protected $section='article-info';
    protected $initial=array('show_date'=>1,'show_author'=>1,'show_category'=>1);
    protected function block_title(){return 'Article Information';}
    public function __construct(){$this->fields=array($this->field('heading','Title (blank = Article Information)'),$this->field('show_date','Show published date','checkbox'),$this->field('show_author','Show author','checkbox'),$this->field('show_category','Show category','checkbox'));parent::__construct();}
    public function widget($args,$instance){if(!is_singular('post'))return;$v=$this->data($instance);$id=$this->post_id();$cats=get_the_category($id);$this->begin($args);echo '<h2>'.$this->heading($v,'Article Information').'</h2><dl>';if($v['show_date'])echo '<dt>Published</dt><dd>'.esc_html(get_the_date('M d, Y',$id)).'</dd>';if($v['show_author'])echo '<dt>Author</dt><dd>'.esc_html(get_the_author_meta('display_name',(int)get_post_field('post_author',$id))).'</dd>';if($v['show_category'])echo '<dt>Category</dt><dd>'.esc_html($cats?implode(', ',wp_list_pluck($cats,'name')):'News').'</dd>';echo '</dl>';$this->end($args);}
}
class PDS_Detail_Recent_News_Widget extends PDS_Detail_Block_Widget {
    protected $section='recent-news';
    protected $initial=array('count'=>3,'show_image'=>1,'orderby'=>'date','order'=>'DESC');
    protected function block_title(){return 'Recent News';}
    public function __construct(){$this->fields=array($this->field('heading','Title (blank = Recent News)'),$this->field('count','Number of posts (1–12)','number'),$this->field('orderby','Sort by','select',array('date'=>'Date','title'=>'Title','modified'=>'Last modified')),$this->field('order','Sort order','select',array('DESC'=>'Descending','ASC'=>'Ascending')),$this->field('show_image','Show thumbnails','checkbox'));parent::__construct();}
    public function widget($args,$instance){if(!is_singular('post'))return;$v=$this->data($instance);$posts=get_posts(array('post_type'=>'post','post_status'=>'publish','numberposts'=>$v['count'],'exclude'=>array($this->post_id()),'orderby'=>$v['orderby'],'order'=>$v['order'],'ignore_sticky_posts'=>true));$this->begin($args);echo '<h2>'.$this->heading($v,'Recent News').'</h2>';if($posts){echo '<div class="pds-news-detail-recent-list">';foreach($posts as $p){echo '<a class="pds-news-detail-recent-item" href="'.esc_url(get_permalink($p->ID)).'">';if($v['show_image'])echo has_post_thumbnail($p->ID)?get_the_post_thumbnail($p->ID,'thumbnail'):'<span class="pds-news-detail-mini-placeholder">NEWS</span>';echo '<span>'.esc_html(get_the_title($p->ID)).'</span></a>';}echo '</div>';}else echo '<p>No recent articles.</p>';$this->end($args);}
}
class PDS_Detail_Related_Heading_Widget extends PDS_Detail_Block_Widget {
    protected $section='before-related';
    protected function block_title(){return 'Related Heading';}
    public function __construct(){$this->fields=array($this->field('heading','Title (blank = NEWEST BLOG ENTRIES)'));parent::__construct();}
    public function widget($args,$instance){if(!is_singular('post'))return;$v=$this->data($instance);$this->begin($args);echo '<h2 id="pds-news-detail-related-title">'.$this->heading($v,'NEWEST BLOG ENTRIES').'</h2>';$this->end($args);}
}
class PDS_Detail_Related_News_Widget extends PDS_Detail_Block_Widget {
    protected $section='related-content';
    protected $initial=array('count'=>6,'same_category'=>1,'orderby'=>'date','order'=>'DESC','show_image'=>1,'show_date'=>1,'show_category'=>1,'show_excerpt'=>1,'show_button'=>1,'button_label'=>'Read More');
    protected function block_title(){return 'Related News Cards';}
    public function __construct(){$this->fields=array($this->field('count','Number of posts (1–12)','number'),$this->field('same_category','Prioritize posts in the same category','checkbox'),$this->field('orderby','Sort by','select',array('date'=>'Date','title'=>'Title','modified'=>'Last modified')),$this->field('order','Sort order','select',array('DESC'=>'Descending','ASC'=>'Ascending')),$this->field('show_image','Show images','checkbox'),$this->field('show_date','Show published dates','checkbox'),$this->field('show_category','Show categories','checkbox'),$this->field('show_excerpt','Show excerpts','checkbox'),$this->field('show_button','Show Read More button','checkbox'),$this->field('button_label','Button label'));parent::__construct();}
    public function widget($args,$instance){
        if(!is_singular('post'))return;$v=$this->data($instance);$id=$this->post_id();$count=$v['count'];$base=array('post_type'=>'post','post_status'=>'publish','numberposts'=>$count,'exclude'=>array($id),'orderby'=>$v['orderby'],'order'=>$v['order'],'ignore_sticky_posts'=>true);
        $posts=array();$cats=get_the_category($id);$cat_ids=wp_list_pluck($cats,'term_id');
        if($v['same_category']&&$cat_ids){$q=$base;$q['category__in']=$cat_ids;$posts=get_posts($q);}
        if(count($posts)<$count){$q=$base;$q['numberposts']=$count-count($posts);$q['exclude']=array_merge(array($id),wp_list_pluck($posts,'ID'));$posts=array_merge($posts,get_posts($q));}
        $this->begin($args);echo '<div class="pds-news-detail-related-grid">';
        foreach($posts as $p){$pid=$p->ID;$url=get_permalink($pid);$pcats=get_the_category($pid);echo '<article class="pds-news-detail-related-card">';if($v['show_image']){echo '<a class="pds-news-detail-related-image" href="'.esc_url($url).'">';echo has_post_thumbnail($pid)?get_the_post_thumbnail($pid,'thumbnail'):'<span class="pds-news-detail-placeholder">NEWS</span>';echo '</a>';}
            echo '<div class="pds-news-detail-related-body"><h3><a href="'.esc_url($url).'">'.esc_html(get_the_title($pid)).'</a></h3>';
            if($v['show_date'])echo '<p class="pds-news-detail-related-date">Published: '.esc_html(get_the_date('M d, Y',$pid)).'</p>';
            if($v['show_category'])echo '<p class="pds-news-detail-related-meta">'.esc_html($pcats?$pcats[0]->name:'News').'</p>';
            if($v['show_excerpt'])echo '<p class="pds-news-detail-related-excerpt">'.esc_html(wp_trim_words(get_the_excerpt($pid),18,'…')).'</p>';
            if($v['show_button'])echo '<a class="pds-news-read-more" href="'.esc_url($url).'">'.esc_html($v['button_label']?:'Read More').'</a>';
            echo '</div></article>';
        }
        echo '</div>';$this->end($args);
    }
}

function pds_detail_dedicated_map(){return array(
    'pds-detail-breadcrumb'=>array('class'=>'PDS_Detail_Breadcrumb_Widget','old'=>'breadcrumb'),
    'pds-detail-header'=>array('class'=>'PDS_Detail_Header_Widget','old'=>'header'),
    'pds-detail-before-article'=>array('class'=>'PDS_Detail_Article_Heading_Widget','old'=>'before-article'),
    'pds-detail-article-content'=>array('class'=>'PDS_Detail_Article_Content_Widget','old'=>'article-content'),
    'pds-detail-after-article'=>array('class'=>'PDS_Detail_After_Article_Widget','old'=>'after-article'),
    'pds-detail-sidebar'=>array('class'=>'PDS_Detail_Side_Intro_Widget','old'=>'sidebar'),
    'pds-detail-article-info'=>array('class'=>'PDS_Detail_Article_Info_Widget','old'=>'article-info'),
    'pds-detail-recent-news'=>array('class'=>'PDS_Detail_Recent_News_Widget','old'=>'recent-news'),
    'pds-detail-before-related'=>array('class'=>'PDS_Detail_Related_Heading_Widget','old'=>'before-related'),
    'pds-detail-related-content'=>array('class'=>'PDS_Detail_Related_News_Widget','old'=>'related-content'),
);}
add_action('widgets_init',function(){foreach(pds_detail_dedicated_map() as $cfg)register_widget($cfg['class']);});

/** Migrate only the old generic widget instances in their matching areas; keep other widgets intact. */
function pds_detail_migrate_dedicated_widgets(){
    if(get_option('pds_detail_dedicated_migrated_v1'))return;
    $areas=wp_get_sidebars_widgets();$old_instances=get_option('widget_pds_detail_section',array());
    foreach(pds_detail_dedicated_map() as $area=>$cfg){
        $class=$cfg['class'];$obj=new $class();$base=$obj->id_base;
        $instances=get_option('widget_'.$base,array());$numeric=array_filter(array_keys($instances),'is_numeric');$next=$numeric?max(array_map('intval',$numeric))+1:2;
        $existing=isset($areas[$area])?(array)$areas[$area]:array();$replacement=array();$has_dedicated=false;
        foreach($existing as $widget_id){
            if(strpos($widget_id,$base.'-')===0){$has_dedicated=true;$replacement[]=$widget_id;continue;}
            if(preg_match('/^pds_detail_section-(\d+)$/',$widget_id,$m)){
                $old=isset($old_instances[(int)$m[1]])?(array)$old_instances[(int)$m[1]]:array();
                if(($old['section']??'')!==$cfg['old']){$replacement[]=$widget_id;continue;}
                $data=array();
                foreach(array('heading','description','count','show_image','show_date','show_author','show_category','show_excerpt','show_button','button_label','button_url','image_id') as $key)if(array_key_exists($key,$old))$data[$key]=$old[$key];
                if(isset($old['html']))$data['extra_html']=$old['html'];
                if($cfg['old']==='article-content')$data['show_content']=1;
                $instances[$next]=$obj->update(array_merge($obj->initial_instance(),$data),array());$replacement[]=$base.'-'.$next;$next++;$has_dedicated=true;
            }else{$replacement[]=$widget_id;}
        }
        if(!$has_dedicated && !$replacement && $area!=='pds-detail-header' && $area!=='pds-detail-sidebar' && $area!=='pds-detail-after-article'){
            $data=array();if($cfg['old']==='article-content')$data['show_content']=1;
            $instances[$next]=$obj->update(array_merge($obj->initial_instance(),$data),array());$replacement[]=$base.'-'.$next;
        }
        if($replacement!==$existing){$areas[$area]=$replacement;update_option('widget_'.$base,$instances);}
    }
    wp_set_sidebars_widgets($areas);update_option('pds_detail_dedicated_migrated_v1',1);
}
add_action('admin_init','pds_detail_migrate_dedicated_widgets',40);

function pds_detail_dedicated_media_assets(){
    wp_enqueue_media();wp_register_script('pds-detail-dedicated-media',false,array('jquery'),'1.0.0',true);wp_enqueue_script('pds-detail-dedicated-media');
    wp_add_inline_script('pds-detail-dedicated-media',<<<'JS'
jQuery(function($){
    $(document).on('click','.pds-detail-choose-image',function(e){e.preventDefault();var control=$(this).closest('.pds-detail-image-control');var input=control.find('.pds-detail-image-id');var preview=control.find('.pds-detail-image-preview');var frame=wp.media({title:'Choose image',button:{text:'Use image'},multiple:false,library:{type:'image'}});frame.on('select',function(){var item=frame.state().get('selection').first().toJSON();input.val(item.id).trigger('change');var src=(item.sizes&&item.sizes.medium?item.sizes.medium.url:item.url);preview.empty().append($('<img>',{src:src,alt:''}).css({maxWidth:'100%',height:'auto'}));});frame.open();});
    $(document).on('click','.pds-detail-remove-image',function(e){e.preventDefault();var control=$(this).closest('.pds-detail-image-control');control.find('.pds-detail-image-id').val(0).trigger('change');control.find('.pds-detail-image-preview').html('<em>Use the default image</em>');});
});
JS
    );
}
add_action('customize_controls_enqueue_scripts','pds_detail_dedicated_media_assets');
add_action('admin_enqueue_scripts',function($hook){if($hook==='widgets.php')pds_detail_dedicated_media_assets();});
