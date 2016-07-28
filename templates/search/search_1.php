<?php
interface iKvSearch
{
    //const HEIGHT_THUMBNAIL = 350;
    //const WIDTH_THUMBNAIL = 350;
    const TEMPLATE = "sidebar-content";
    const SIDEBAR = "search-sidebar";
    const SENTANCE_BREADCRUMB = "Search for";
    const SENTANCE_TITLE_SEARCH_PAGE = "Search";
    const CLASS_ADD_PRICE = "search-price";
    const CLASS_THE_EXCERPT = "search-excerpt";
    const CLASS_SEARCH_BAR = "search-form-bar";
    const CLASS_SIDEBAR = "search-sidebar-area";
    const CLASS_RESULT = "search-result-area";
    
    /**
     * Adding the price for the product under the img 
     **/
    public function kv_add_price();
    
    /**
     * Modifing the breadcrumb for adding the new sentance
     * $output string the actual breadcrumb
     **/
    public function kv_correctif_wpseo_breadcrumb_output_search( $output );
    
    /**
     * Adding the full search bar on the page
     **/
    public function kv_add_search_bar();
    
    /**
     * Modifing the title of the search page
     **/
    public function kv_genesis_do_search_title();
    
    /**
     * Removing the share button on the post
     **/
    public function kv_remove_share_buttons();
    
    /**
     * Modifing the size of the thumbnail
     **/
    public function kv_modification_image_thumbnail();
    
    /**
     * Displaying the excerpt for post and custom post type
     **/
    public function kv_the_excerpt();
    
    /**
     * Displaying the search-sidebar
     **/
    public function kv_display_search_sidebar();
    
    /**
     * Opening the markup for creating an area
     **/
    public function kv_search_markup_open();
    
    /**
     * Closing the markup of the area
     **/
    public function kv_search_markup_close();
    
    /**
     * Forcing the layout
     **/
    public function kv_force_layout();
    
    /**
     * Force la sidebar
     **/
    public function kv_genesis_do_sidebar_search();
}


class KvCustomSearch implements iKvSearch
{
    public function __construct()
	{   
	    /** REMOVE ACTION & FILTER **/
        remove_action('genesis_entry_content','genesis_do_post_content');
        remove_action('genesis_entry_footer','genesis_entry_footer_markup_open', 5);
        remove_action('genesis_entry_footer','genesis_entry_footer_markup_close', 15);
        remove_action('genesis_entry_footer','genesis_post_meta');
        remove_action('genesis_before_loop','genesis_do_breadcrumbs');
        unregister_sidebar( 'sidebar' );
        
	    /** ADD ACTION & FILTER **/
	    add_filter( 'genesis_pre_get_option_site_layout', array($this, 'kv_force_layout'));
	    add_action( 'genesis_sidebar', array($this,'kv_genesis_do_sidebar_search'));
	    add_action('genesis_entry_content',array($this,'kv_the_excerpt'));
        add_action( 'genesis_entry_content', array($this, 'kv_add_price'));
        add_filter( 'wpseo_breadcrumb_output', array($this, 'kv_correctif_wpseo_breadcrumb_output_search'),10000);
        add_action( 'genesis_before_content', 'genesis_do_breadcrumbs');
        add_action( 'genesis_before_content', array($this,'kv_genesis_do_search_title'));
        add_action( 'genesis_before_content', array($this,'kv_add_search_bar'));
        add_action( 'genesis_before', array($this,'kv_remove_share_buttons'));
        add_filter( 'genesis_pre_get_option_image_size', array($this, 'kv_modification_image_thumbnail'));
        add_action('genesis_loop',array($this, 'kv_search_markup_open'),-1);
        add_action('genesis_loop',array($this, 'kv_search_markup_close'),10000);
	}
	
    public function kv_add_price() {
        global $post, $product;
        if($post->post_type=="product") {
            echo sprintf('<div class="%s">%s</div>',iKvSearch::CLASS_ADD_PRICE,$product->get_price_html());
        }
    } 
    
    public function kv_correctif_wpseo_breadcrumb_output_search( $output ){
        return str_replace("You searched for",iKvSearch::SENTANCE_BREADCRUMB, $output);
    }
    
    public function kv_add_search_bar() {
        echo sprintf('<div class="%s">',iKvSearch::CLASS_SEARCH_BAR);
        echo sprintf("%s",get_search_form());
        echo sprintf('</div>');
    }
    
    public function kv_genesis_do_search_title() {
	    $title = sprintf( '<div class="archive-description"><h1 class="archive-title">%s</h1></div>', apply_filters( 'genesis_search_title_text', __( iKvSearch::SENTANCE_TITLE_SEARCH_PAGE, 'genesis' ) ), get_search_query() );
	    echo apply_filters( 'genesis_search_title_output', $title ) . "\n";
    }
    
    public function kv_remove_share_buttons() {
    	global $Genesis_Simple_Share;
    	remove_action( 'genesis_loop', array( $Genesis_Simple_Share, 'start_icon_actions' ), 5 );
    	remove_action( 'genesis_loop', array( $Genesis_Simple_Share, 'end_icon_actions' ), 15 );
    }
    
    public function kv_modification_image_thumbnail() {
        //add_image_size( 'custom-search', iKvSearch::WIDTH_THUMBNAIL, iKvSearch::HEIGHT_THUMBNAIL, true );
    	return 'thumbnail';
    }
    
    public function kv_the_excerpt() {
        global $post;
        if($post->post_type!="product") {
            echo sprintf('<div class="%s">%s</div>',iKvSearch::CLASS_THE_EXCERPT,$post->post_excerpt);
        }        
    }
    
    public function kv_search_markup_open() {
        echo sprintf('<div class="%s">',iKvSearch::CLASS_RESULT);    
    }
    
    public function kv_search_markup_close() {
        echo sprintf('</div>');     
    }
    
    public function kv_display_search_sidebar() {
	    if(is_active_sidebar( 'search-sidebar' ) ) :
	        echo sprintf('<div id="search-sidebar" class="primary-sidebar widget-area product-widget-area" role="complementary">');
	        dynamic_sidebar( 'search-sidebar' );
	        echo sprintf('</div>');
        endif;
    }
    
    public function kv_force_layout() {
        return iKvSearch::TEMPLATE;    
    }
    
    public function kv_genesis_do_sidebar_search() {
        dynamic_sidebar(iKvSearch::SIDEBAR);    
    }
}
new KvCustomSearch();

genesis();