<?php
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
if(!class_exists('Superaddons_Check_Purchase_Code')){
	class Superaddons_Check_Purchase_Code {
		protected $data;
		public function __construct( $data ) { 
			$defaults = array(
				"plugin"=>false,
				"id"=>false,
				"bundle"=>false,
				"pro" => "",
				"document"=>"#"
			);
			$args = wp_parse_args( $data, $defaults );
			$this->data = $args;	
			add_filter( 'plugin_action_links_' . $this->data["plugin"] , array( $this, 'add_action' ) );
			add_action( 'wp_ajax_rednumber_check_purchase_code', array($this,'check_purchase_code_ajax') );
			add_action( 'wp_ajax_rednumber_check_purchase_code_remove', array($this,'check_purchase_remove_code_ajax') );
			add_action( 'wp_ajax_yeeaddons_remove_notice', array($this,'yeeaddons_remove_notice') );
			add_action( 'wp_ajax_yeeaddons_remove_notice_review', array($this,'yeeaddons_remove_notice_review') );
			add_action('admin_enqueue_scripts', array($this,'add_js'));
			add_action( 'admin_notices', array($this,"add_pro") );
			//add_action( 'admin_notices', array($this,"add_review") );
			add_action( 'admin_head', array($this,"add_head") );
			//add_action( 'activate_'.$args["plugin"],array( $this, 'pad_plugin_status_changed' ) );
		}
		function pad_plugin_status_changed(){
			update_option( '_yeeaddons_time_item_'.$this->data["id"],current_time( 'timestamp' ));
		}
		function add_head() {
			?>
			<style type="text/css">
				.pro_disable::after {
					content: "Pro";
					position: absolute;
					bottom: 0;
					right: 0;
					background: red;
					padding: 3px;
					font-size: 11px;
					color: #fff;
					border-radius: 5px 0 0 0;
				}
				.pro_disable {
					position: relative;
				}
				.pro_disable_padding{
					padding: 10px !important;
				}
				.pro_text_style{
					color:#9f9e9e;
				}
				body .pro_disable_fff {
					background: transparent;
				}
				#yeeaddons-slug-wrapper {
				position: relative;
				min-width: calc(100% - 80px);
				}

				.yeeaddons-nice-review * {
				box-sizing: border-box;
				-moz-box-sizing: border-box;
				-webkit-box-sizing: border-box;
				}

				.yeeaddons-nice-review *:not(.yeeaddons-background-pattern) {
				z-index: 1;
				}

				.yeeaddons-nice-review.yeeaddons-CDP {
				--accent-color: #6BB4A7;
				--accent-color-hover: #5fa296;
				}

				.yeeaddons-nice-review.yeeaddons-BM {
				--accent-color: #0F9990;
				--accent-color-hover: #0d877f;
				}

				.yeeaddons-nice-review-wrapper {
				width: calc(100% - 20px);
				padding-right: 20px;
				justify-content: center;
				margin-top: 30px;
				}

				.yeeaddons-nice-review-wrapper,
				.yeeaddons-nice-review,
				.yeeaddons-content,
				.yeeaddons-options-section {
				display: -webkit-inline-flex;
				display: -ms-inline-flex;
				display: inline-flex;
				}

				.yeeaddons-nice-review {
				background-color: #ffffff;
				overflow: hidden;
				position: relative;
				padding: 25px;
				max-width: 975px;
				}

				.yeeaddons-nice-review .yeeaddons-background-pattern {
				position: absolute;
				top: 0;
				right: 0;
				}

				.yeeaddons-CDP .yeeaddons-BM-background {
				display: none;
				}

				.yeeaddons-nice-review .yeeaddons-BM-background {
				position: absolute;
				bottom: 0;
				right: 0;
				width: 300px;
				}

				.yeeaddons-nice-review .yeeaddons-main-image-content {
				position: relative;
				}
				img.yeeaddons-main-image {
					max-width: 150px;
				}
				.yeeaddons-nice-review .yeeaddons-main-image-part-1,
				.yeeaddons-nice-review .yeeaddons-main-image-part-2,
				.yeeaddons-nice-review .yeeaddons-main-image-part-3,
				.yeeaddons-nice-review .yeeaddons-main-image-part-4 {
				position: absolute;
				}

				.yeeaddons-nice-review  .yeeaddons-main-image-part-1 {
				top: 4px;
				left: 20px;
				}

				.yeeaddons-nice-review  .yeeaddons-main-image-part-1 path,
				.yeeaddons-nice-review  .yeeaddons-main-image-part-3 path {
				fill: var(--accent-color);
				}

				.yeeaddons-nice-review  .yeeaddons-main-image-part-2 {
				top: -7px;
				right: -4px;
				}

				.yeeaddons-nice-review  .yeeaddons-main-image-part-3 {
				bottom: 24px;
				right: 0px;
				}

				.yeeaddons-nice-review  .yeeaddons-main-image-part-4 {
				bottom: 0px;
				left: 6px;
				}

				.yeeaddons-nice-review .yeeaddons-content {
				-webkit-flex-direction: column;
				-ms-flex-direction: column;
				flex-direction: column;
				}

				.yeeaddons-nice-review .yeeaddons-text p {
				font-weight: 300;
				color: #000000;
				font-weight: 300;
				font-size: 15px;
				letter-spacing: -0.6px;
				margin: 0;
				}

				.yeeaddons-nice-review .yeeaddons-text p a {
				font-weight: 700;
				color: var(--accent-color);
				text-decoration: none;
				}

				.yeeaddons-nice-review .yeeaddons-text p a:hover {
				color: var(--accent-color-hover);
				}

				.yeeaddons-nice-review .yeeaddons-text p a:hover {
				text-decoration: underline;
				}

				.yeeaddons-nice-review .yeeaddons-text p b {
				font-weight: 800;
				}

				.yeeaddons-nice-review .yeeaddons-text p:first-child {
				margin-top: 8px;
				}

				.yeeaddons-nice-review .yeeaddons-text p:not(:first-child) {
				margin-top: 10px;
				}

				.yeeaddons-nice-review .yeeaddons-options-section {
				max-width: 660px;
				justify-content: space-between;
				align-items: center;
				}

				.yeeaddons-nice-review .yeeaddons-option-1 {
				position: relative;
				text-decoration: none;
				}

				.yeeaddons-nice-review .yeeaddons-round-button,
				.yeeaddons-nice-review .yeeaddons-option-1 svg text {
				font-weight: 800;
				font-size: 16px;
				line-height: 16px;
				color: #FFFFFF;
				}

				.yeeaddons-nice-review .yeeaddons-round-button {
				background: var(--accent-color);
				border-radius: 100px;
				border: 0;
				outline: none;
				cursor: pointer;
				}

				.yeeaddons-nice-review .yeeaddons-option-1 svg path {
				fill: var(--accent-color);
				}

				.yeeaddons-nice-review .yeeaddons-option-1:hover .yeeaddons-round-button {
				background: var(--accent-color-hover);
				}

				.yeeaddons-nice-review .yeeaddons-option-1:hover svg path {
				fill: var(--accent-color-hover);
				}

				.yeeaddons-nice-review .yeeaddons-option-1:hover span {
				color: var(--accent-color-hover);
				}

				.yeeaddons-nice-review .yeeaddons-option-1 span {
				font-weight: 500;
				font-size: 12px;
				line-height: 12px;
				color: var(--accent-color);
				position: absolute;
				top: calc(100% + 5px);
				left: 0;
				width: 100%;
				text-align: center;
				cursor: pointer;
				}

				.yeeaddons-nice-review .yeeaddons-option-2 button,
				.yeeaddons-nice-review .yeeaddons-option-3 button,
				.yeeaddons-nice-review .yeeaddons-option-4 button {
				font-weight: 300;
				font-size: 13px;
				line-height: 13px;
				color: #373737;
				cursor: pointer;
				outline: none;
				}

				.yeeaddons-nice-review .yeeaddons-option-2 button:hover,
				.yeeaddons-nice-review .yeeaddons-option-3 button:hover,
				.yeeaddons-nice-review .yeeaddons-option-4 button:hover {
				opacity: .8;
				}

				@media (min-width: 1301px) {
				.yeeaddons-nice-review .yeeaddons-background-pattern {
					width: 85%;
				}

				.yeeaddons-nice-review .yeeaddons-text {
					padding-right: 20px;
				}

				.yeeaddons-nice-review .yeeaddons-text p {
					line-height: 20px;
				}

				.yeeaddons-nice-review .yeeaddons-options-section {
					margin-top: 17px;
				}

				.visible-max-1300 {
					display: none;
				}
				}

				@media (max-width: 1300px) {
				.yeeaddons-nice-review {
					padding-bottom: 40px;
				}

				.yeeaddons-main-image-wrapper {
					margin-top: 20px;
				}

				.yeeaddons-nice-review .yeeaddons-text p {
					line-height: 22px;
				}

				.yeeaddons-nice-review .yeeaddons-options-section {
					margin-top: 25px;
				}
				}

				@media (min-width: 645px) and (max-width: 1300px) {
				.yeeaddons-nice-review .yeeaddons-background-pattern {
					max-width: 85%;
					height: 100%;
				}
				}

				@media (min-width: 1030px) and (max-width: 1300px), (min-width: 896px) and (max-width: 960px) {
				.yeeaddons-nice-review .yeeaddons-text {
					padding-right: 70px;
				}
				}

				@media (min-width: 1030px), (min-width: 896px) and (max-width: 960px) {
				.yeeaddons-nice-review {
					border-radius: 20px;
				}

				.yeeaddons-nice-review .yeeaddons-content {
					margin-left: 30px;
				}

				.yeeaddons-nice-review .yeeaddons-option-1 svg {
					display: none;
				}
				}

				@media (min-width: 961px) and (max-width: 1029px), (min-width: 645px) and (max-width: 895px) {
				.yeeaddons-nice-review {
					padding-bottom: 90px;
					border-radius: 10px;
				}

				.yeeaddons-nice-review .yeeaddons-content {
					margin-left: 40px;
				}

				.yeeaddons-nice-review .yeeaddons-leave-review-link,
				.yeeaddons-nice-review .yeeaddons-option-1 {
					display: -webkit-inline-flex;
					display: -ms-inline-flex;
					display: inline-flex;
				}

				.yeeaddons-nice-review .yeeaddons-option-1 .yeeaddons-round-button {
					display: none;
				}

				.yeeaddons-nice-review .yeeaddons-option-2 {
					margin-left: 15px;
				}

				.yeeaddons-nice-review .yeeaddons-options-section {
					position: absolute;
					left: 0;
					bottom: 35px;
				}
				}

				@media (min-width: 701px) {
				.yeeaddons-nice-review .yeeaddons-text p {
					font-size: 15px;
				}
				}

				@media (max-width: 700px) {
				.yeeaddons-nice-review .yeeaddons-text p {
					font-size: 13px;
				}
				}

				@media (min-width: 645px) {
				.yeeaddons-nice-review .yeeaddons-round-button {
					padding: 7px 32px;
				}

				.yeeaddons-nice-review .yeeaddons-option-3,
				.yeeaddons-nice-review .yeeaddons-option-4 {
					margin-left: 20px;
				}

				.yeeaddons-nice-review .yeeaddons-option-2 button,
				.yeeaddons-nice-review .yeeaddons-option-3 button,
				.yeeaddons-nice-review .yeeaddons-option-4 button {
					background-color: transparent;
					border: 0;
					padding: 0;
				}
				}

				@media (max-width: 644px) {
				.yeeaddons-nice-review,
				.yeeaddons-nice-review .yeeaddons-options-section {
					-webkit-flex-direction: column;
					-ms-flex-direction: column;
					flex-direction: column;
				}

				.yeeaddons-nice-review .yeeaddons-background-pattern {
					max-width: 660px;
					height: 100%;
				}

				.yeeaddons-nice-review .yeeaddons-main-image-wrapper {
					display: -webkit-inline-flex;
					display: -ms-inline-flex;
					display: inline-flex;
					justify-content: center;
				}

				.yeeaddons-nice-review .yeeaddons-round-button {
					padding: 7px 48px;
				}

				.yeeaddons-nice-review .yeeaddons-option-2 {
					margin-left: 0;
					margin-top: 30px;
				}

				.yeeaddons-nice-review .yeeaddons-option-3,
				.yeeaddons-nice-review .yeeaddons-option-4 {
					margin-top: 10px;
				}

				.yeeaddons-nice-review .yeeaddons-option-2 button,
				.yeeaddons-nice-review .yeeaddons-option-3 button,
				.yeeaddons-nice-review .yeeaddons-option-4 button {
					width: 220px;
					padding: 7px;
					border-radius: 100px;
					background-color: rgba(250, 250, 250, .7);
					border: 1px solid #eee;
				}

				.yeeaddons-nice-review .yeeaddons-option-1 svg {
					display: none;
				}
				}

				@media (max-width: 400px) {
				.yeeaddons-nice-review .yeeaddons-background-pattern {
					display: none;
				}
				}

			</style>
			<?php
		}
		private function can_be_displayed() {
			global $pagenow;
	        $admin_pages = array('index.php', 'plugins.php');
			$check = get_option( '_redmuber_item_'.$this->data["id"] );
	        if($check != "ok" ) {
		        if ( in_array( $pagenow, $admin_pages )) {
					$user_id = get_current_user_id();
					$check_disable = get_option( "yeeaddons_".$this->data["id"]."_".$user_id."_review" );
					if(!$check_disable){
						$time = get_option( '_yeeaddons_time_item_'.$this->data["id"]);
						if($time){
							$time = strtotime('-30 days', $time);
							$current_time = current_time( 'timestamp' );
							if($time < $current_time){
								return true;
							}
						}
					}
				}
			}
			return false;
		}
		function add_review(){
			if ($this->can_be_displayed()) {
				$datas_plugins = explode("/",$this->data["plugin"]);
				$url_plugin = "https://wordpress.org/plugins/".$datas_plugins[0];
				$url_plugin_review = "https://wordpress.org/support/plugin/".$datas_plugins[0]."/reviews/?filter=5";
			?>
			<section class="yeeaddons-nice-review-wrapper" id="yeeaddons-slug-wrapper" data-slug="<?php echo esc_html($this->slug); ?>">
				<div class="yeeaddons-nice-review yeeaddons-CDP" id="yeeaddons-sub-wrapper">
					<img src="https://cdn.add-ons.org/rate.webp" alt="Background with stars" class="yeeaddons-main-image">
					<div class="yeeaddons-main-image-wrapper">
					<div class="yeeaddons-main-image-content">
						<svg class="yeeaddons-main-image-part-1" width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" clip-rule="evenodd" d="M0.16333 20.7604V0.976166L19.9476 10.8713L0.16333 20.7604ZM3.9547 7.10886V14.6277L11.4697 10.8713L3.9547 7.10886Z" fill="#6BB4A7"/>
						</svg>
						<svg class="yeeaddons-main-image-part-3" width="32" height="12" viewBox="0 0 32 12" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" clip-rule="evenodd" d="M7.05762 11.0941L0.098938 4.57857L2.51846 2.03343L7.05821 6.28666L12.8091 0.902252L18.5583 6.28666L24.3115 0.902252L31.2741 7.41783L28.8552 9.96297L24.3115 5.70974L18.5578 11.0941L12.8086 5.70974L7.05762 11.0941Z" fill="#6BB4A7"/>
						</svg>
					</div>
					</div>
					<div class="yeeaddons-content">
					<div class="yeeaddons-text">
						<p>You’ve been using the <a href="<?php echo esc_url($url_plugin) ?>"><?php echo esc_attr($this->data["plugin_name"]) ?></a> plugin for over <?php echo 4 ?> days now – entirely <b>for FREE :)</b></p>
						<p>Don’t worry, we’re not asking you to upgrade to premium.<br class="visible-max-1300"> But maybe you can give us a nice review (if you like it)? We put a lot of effort into the free plugin, and made it feature-rich. It would motivate us a lot! Is that fair?</p>
					</div>
					<div class="yeeaddons-options-section">
						<a href="<?php echo esc_url($url_plugin_review) ?>" target="_blank" class="yeeaddons-leave-review-link yeeaddons-dismiss yeeaddons-notice-warning-remove-review" data-id="<?php echo esc_attr( $this->data["id"] ) ?>">
						<div class="yeeaddons-option-1">
							<button class="yeeaddons-round-button yeeaddons-notice-warning-remove-review" data-id="<?php echo esc_attr( $this->data["id"] ) ?>">Yes, that's fair!</button>
							<svg width="185" height="34">
							<path d="M 0 0 L 185 0 L 165 17 L 185 34 L 0 34 Z" />
							<text x="85" y="19" fill="#FFFFFF" text-anchor="middle" alignment-baseline="middle">
								Yes, that's fair!
							</text>
							</svg>
							<span>Let me give a nice review</span>
						</div>
						</a>
						<div class="yeeaddons-option-4">
						<button class="yeeaddons-dismiss yeeaddons-notice-warning-remove-review" data-id="<?php echo esc_attr( $this->data["id"] ) ?>">I'm just taking, not giving</button>
						</div>
					</div>
					</div>
				</div>
				</section>
			<?php
			}
		}
		function add_pro(){
	        global $pagenow;
	        $admin_pages = array('index.php', 'plugins.php');
	        $check = get_option( '_redmuber_item_'.$this->data["id"] );
	        if($check != "ok" ) {
		        if ( in_array( $pagenow, $admin_pages )) {
					$user_id = get_current_user_id();
					$check = get_option( "yeeaddons_".$this->data["id"]."_".$user_id );
					if($check && $pagenow == "index.php"){
						//todo
					}else{
					?>
					<div class="notice notice-warning is-dismissible yeeaddons-notice-warning-remove" data-id="<?php echo esc_attr( $this->data["id"] ) ?>">
						<p><strong><?php echo esc_attr($this->data["plugin_name"]) ?>: </strong><?php esc_html_e( 'Enter Purchase Code below the plugin  or Upgrade to pro version: ', 'rednumber' ); ?> <a href="<?php echo esc_url( $this->data["pro"] ) ?>" target="_blank" ><?php echo esc_url( $this->data["pro"] ) ?></a></p>
					</div>
					<?php
					}
		    	}
	    	}
	    }
		function yeeaddons_remove_notice(){
			$id = sanitize_text_field($_POST["plugin_id"]);
			$user_id = get_current_user_id();
			update_option( "yeeaddons_".$id."_".$user_id, current_time( 'timestamp' ) );
			$check = get_option( "yeeaddons_".$id."_".$user_id );
			die();
		}
		function yeeaddons_remove_notice_peview(){
			$id = sanitize_text_field($_POST["plugin_id"]);
			$user_id = get_current_user_id();
			update_option( "yeeaddons_".$id."_".$user_id, current_time( 'timestamp' ) );
			$check = get_option( "yeeaddons_".$id."_".$user_id."_review" );
			die();
		}
		function add_js(){
			wp_enqueue_script('rednumber_check_purchase_code', plugins_url('rednumber_check_purchase_code.js', __FILE__),array("jquery"));
		}
		function add_action($links){
			$check = get_option( '_redmuber_item_'.$this->data["id"] );
			$class_1 = "";
			$class_2 = "";
			if( $check =="ok" ){
				$class_1 = "hidden";
			}else{
				$class_2 = "hidden";
			}
			$mylinks = array(
			        '<div class="rednumber-purchase-container rednumber-purchase-container_form '.$class_1.'">'.esc_html__("Purchase Code:","rednumber").' <input data-id="'.$this->data["id"].'" type="text"><a href="#" class="button button-primary rednumber-active">'.esc_html__("Active","rednumber").'</a></div>
			         <div class="rednumber-purchase-container rednumber-purchase-container_show '.$class_2.'">Purchased: <span>'.get_option( '_redmuber_item_'.$this->data["id"]."_code" ).'</span> <a data-code="'.get_option( '_redmuber_item_'.$this->data["id"]."_code" ).'" data-id="'.$this->data["id"].'" href="#" class="rednumber-remove">'.esc_html__("Remove","rednumber").'</a></div><a target="_blank" class="'.$class_1.'"  href="'.$this->data["pro"].'" >'.esc_html__("Get pro version","rednumber").'</a>',
			    );
			$mylinks[] ='<a href="'.$this->data["document"] .'" target="_blank" />Document</a>';
		    return array_merge( $links, $mylinks );
		}
		function check_purchase_code_ajax(){
			$code = sanitize_text_field($_POST["code"]);
			$id = sanitize_text_field($_POST["id"]);
			$status = $this->check_purchase_code($code,$id);
			if( $status == "ok"){
				update_option( '_redmuber_item_'.$id, "ok" );
				update_option( '_redmuber_item_'.$id."_code", $code );
			}
			echo esc_attr($status);
			die();
		}
		function check_purchase_remove_code_ajax(){
			$id = sanitize_text_field($_POST["id"]);
			$code = sanitize_text_field($_POST["code"]);
			delete_option('_redmuber_item_'.$id);
			delete_option('_redmuber_item_'.$id."_code");
			$personalToken = "uzAMx8rZ3FRV0ecu8t1pXNWG0d0NA6qL";
			$userAgent = "Purchase code verification";
			$ch = curl_init();
			$domain_name = preg_replace('/^www\./','',$_SERVER['SERVER_NAME']);
			curl_setopt_array($ch, array(
			    CURLOPT_URL => "https://add-ons.org/wp-json/removepurchase_code/apiv2/token/{$code}/".htmlentities($domain_name),
			    CURLOPT_RETURNTRANSFER => true,
			    CURLOPT_TIMEOUT => 20,
			    CURLOPT_HTTPHEADER => array(
			        "Authorization: Bearer {$personalToken}",
			        "User-Agent: {$userAgent}"
			    )
			));
			$response = curl_exec($ch);
			if (curl_errno($ch) > 0) { 
			    return "Error connecting to API: " . curl_error($ch);
			}
			$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if ($responseCode === 404) {
			    return "The purchase code was invalid";
			}
			if ($responseCode !== 200) {
			    return "Failed to validate code due to an error: HTTP {$responseCode}";
			}
			$body = json_decode($response,true);
			if ($body === false && json_last_error() !== JSON_ERROR_NONE) {
			    return "Error parsing response";
			}
			die();
		}
		function resolve_root_domain($url, $max=6){
		$domain = parse_url($url, PHP_URL_HOST);
		if (!strstr(substr($domain, 0, $max), '.'))
			return ($domain);
		else
			return (preg_replace("/^(.*?)\.(.*)$/", "$2", $domain));
		}
		function check_purchase_code($code,$id_item){
			$personalToken = "uzAMx8rZ3FRV0ecu8t1pXNWG0d0NA6qL";
			$userAgent = "Purchase code verification";
			$ch = curl_init();
			$domain_name = preg_replace('/^www\./','',$_SERVER['SERVER_NAME']);
			curl_setopt_array($ch, array(
			    CURLOPT_URL => "https://add-ons.org/wp-json/checkpurchase_code/apiv2/token/{$code}/".htmlentities($domain_name)."/".$id_item,
			    CURLOPT_RETURNTRANSFER => true,
			    CURLOPT_TIMEOUT => 20,
			    CURLOPT_HTTPHEADER => array(
			        "Authorization: Bearer {$personalToken}",
			        "User-Agent: {$userAgent}"
			    )
			));
			$response = curl_exec($ch);
			if (curl_errno($ch) > 0) { 
			    return "Error connecting to API: " . curl_error($ch);
			}
			$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if ($responseCode === 404) {
			    return "The purchase code was invalid";
			}
			if ($responseCode !== 200) {
			    return "Failed to validate code due to an error: HTTP {$responseCode}";
			}
			$body = json_decode($response,true);
			if ($body === false && json_last_error() !== JSON_ERROR_NONE) {
			    return "Error parsing response";
			}
			if( isset($body["check"]) && $body["check"] == "ok" ){
				return "ok";
			}else{
				if( isset($body["check"]) ){
					return $body["check"];
				}else{
					return "Please choose other purchase.";
				}
			}
		}
	}
}