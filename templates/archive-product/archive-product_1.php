<?php
/**
 * @package genesis_connect_woocommerce
 * @version 2.0.0
 */ 

/**
 * The interface with all the function added for this template
 **/
interface iKvArchiveProduct
{
    const TEMPLATE = "content-sidebar";
    const SIDEBAR = "sidebar-shop";
    
    /**
     * Adding the actions and filters associated with this template
     **/
    public function kv_add_action_filter();
    
    /**
     * Removing the actions and filters associated with this template 
     **/
    public function kv_remove_action_filter();
    
    /**
     * Forcing the layout of the template
     **/
    public function kv_force_layout();
    
    /**
     * Forcing the sidebar of the template
     **/
    public function kv_genesis_do_sidebar_custom();
    
    /**
     * Wrapping the description
     **/
    public function kv_woocommerce_product_archive_description_custom();
    
    public function kv_woocommerce_pagination_custom(); 
}

class KvCustomArchiveProduct implements iKvArchiveProduct
{
    public function __construct()
	{   
	    $this->kv_remove_action_filter();
	    $this->kv_add_action_filter();
	}
	
	public function kv_add_action_filter() {
	    add_filter( 'genesis_pre_get_option_site_layout', array($this, 'kv_force_layout'));
	    add_action( 'genesis_sidebar', array($this,'kv_genesis_do_sidebar_custom'));
	    add_action('genesis_after_loop',array($this,'kv_woocommerce_product_archive_description_custom'));
	    add_action( 'genesis_before_loop', array($this,'genesiswooc_archive_product_loop'));
	    //add_action( 'woocommerce_after_shop_loop', array($this,'kv_woocommerce_pagination_custom'), 11 );
	}
	
	public function kv_remove_action_filter() {
	    remove_action('woocommerce_before_shop_loop','woocommerce_result_count',20);
	    add_action('get_header',array($this,'kv_change_genesis_sidebar'), 11);
	    remove_action( 'genesis_loop', 'genesis_do_loop' );
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
		remove_action( 'woocommerce_archive_description', 'woocommerce_product_archive_description',10);
		//remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
	}
	
	public function kv_change_genesis_sidebar() {
	    remove_action( 'genesis_sidebar', 'gencwooc_ss_do_sidebar' );
    }
	
	public function kv_force_layout() {
        return iKvArchiveProduct::TEMPLATE;    
    }
    
    public function kv_genesis_do_sidebar_custom() {
        dynamic_sidebar(iKvArchiveProduct::SIDEBAR);    
    }
    
    public function kv_woocommerce_pagination_custom() {
    	echo sprintf('<div class="pagination_row">');
    	woocommerce_pagination();
    	echo sprintf('</div>');
    }
    
    public function kv_woocommerce_product_archive_description_custom() {
	    if ( is_post_type_archive( 'product' ) ) { 
	        $shop_page = get_post( wc_get_page_id( 'shop' ) ); 
	        if ( $shop_page ) { 
	            $description = wc_format_content( $shop_page->post_content ); 
	            if ( $description ) { 
	                echo sprintf('<div class="page-description"><div class="archive-description-class"><div class="term-description-class">%s</div></div></div>',$description); 
	            } 
	        } 
	    } 
	}
	
	/**
	 * Display shop items (product custom post archive)
	 *
	 * This function has been refactored in 0.9.4 to provide compatibility with
	 * both WooC 1.6.0 and backwards compatibility with older versions.
	 * This is needed thanks to substantial changes to WooC template contents
	 * introduced in WooC 1.6.0.
	 *
	 * @uses genesiswooc_content_product() if WooC is version 1.6.0+
	 * @uses genesiswooc_product_archive() for earlier WooC versions
	 *
	 * @since 0.9.0
	 * @updated 0.9.4
	 * @global object $woocommerce
	 */
	public function genesiswooc_archive_product_loop() {
	
		global $woocommerce;
		
		$new = version_compare( $woocommerce->version, '1.6.0', '>=' );
		
		if ( $new )
			genesiswooc_content_product();
			
		else
			genesiswooc_product_archive();
	}
}
new KvCustomArchiveProduct();

genesis();