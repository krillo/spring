<?php

/*
 * Walker for the Front End MashMenu
 */
class MashMenuWalkerCore extends Walker_Nav_Menu{

	protected $index = 0;
	protected $menuItemOptions;
	protected $noMashMenu;

	/**
	 * Traverse elements to create list from elements.
	 *
	 * Calls parent function in wp-includes/class-wp-walker.php
	 */
	function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {

		if ( !$element )
			return;

		//Add indicators for top level menu items with submenus
		$id_field = $this->db_fields['id'];
		$element->classes[] = 'level' . $depth;
		if ( $depth == 0 && !empty( $children_elements[ $element->$id_field ] ) ) {
			$element->classes[] = 'has-sub';
		}
		
		$id_field = $this->db_fields['id'];

		//display this element
		if ( is_array( $args[0] ) )
			$args[0]['has_children'] = ! empty( $children_elements[$element->$id_field] );
		
		if($this->getMashMenuOption($element->menu_item_parent,'isMega') != 'off'){
			if($depth == 1 && is_array($args[0]))
				$args[0]['parentMega'] = 'on';
		}
		
		$cb_args = array_merge( array(&$output, $element, $depth), $args);		
		
		call_user_func_array(array($this, 'start_el'), $cb_args);

		$id = $element->$id_field;

		// descend only when the depth is right and there are childrens for this element
		if ( ($max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id]) ) {

			foreach( $children_elements[ $id ] as $child ){

				if ( !isset($newlevel) ) {
					$newlevel = true;
					//start the child delimiter
					
					$args = array(array("id"=>$element->$id_field,"title"=>$element->title));	

					if($this->getMashMenuOption($element->$id_field,'isMega') != 'off'){
						$args[0]["parentMega"] = 'on';
					}
					
					$cb_args = array_merge( array(&$output, $depth), $args);
					
					call_user_func_array(array($this, 'start_lvl'), $cb_args);
				}
				$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
			}
			unset( $children_elements[ $id ] );
		}

		if ( isset($newlevel) && $newlevel ){
			//end the child delimiter
			$args = array(array("id"=>$element->$id_field,"title"=>$element->title));					
			if($this->getMashMenuOption($element->$id_field,'isMega') != 'off'){
				$args[0]["parentMega"] = 'on';
			}
			$cb_args = array_merge( array(&$output, $depth), $args);
			call_user_func_array(array($this, 'end_lvl'), $cb_args);
		}

		//end this element
		$cb_args = array_merge( array(&$output, $element, $depth), $args);
		call_user_func_array(array($this, 'end_el'), $cb_args);
	}

	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		if($depth == 0){
			if(isset($args["parentMega"]) && $args["parentMega"] == 'on'){
				$output .= "\n$indent<div class=\"sub-content\"><ul class=\"sub-channel\">";
			} else {
				$output .= "\n$indent<div class=\"sub-content\"><ul class=\"columns\">\n";
			}
		} else {
			$output .= "\n$indent<li><ul class=\"list\"><li class=\"header\">".$args["title"]."</li>\n";
		}
	}
	
	function end_lvl( &$output, $depth = 0, $args = array() ){
		$indent = str_repeat( "\t", $depth );
		if($depth == 0){
			if(isset($args["parentMega"]) && $args["parentMega"] != 'off'){
				$output .= "\n$indent</ul></div>"; // end <ul class="sub-channel">
			} else {
				$output .= "\n$indent</ul></div>\n"; // end <ul class="columns">
			}
		} else {
			$output .= "\n$indent</ul>\n";
		}
	}

	function getMashMenuOption( $item_id , $id ){
		$option_id = 'menu-item-'.$id;

		//Initialize array
		if( !is_array( $this->menuItemOptions ) ){
			$this->menuItemOptions = array();
			$this->noMashMenu = array();
		}

		//We haven't investigated this item yet
		if( !isset( $this->menuItemOptions[ $item_id ] ) ){
			
			$mashmenu_options = false;
			if( empty( $this->noMashMenu[ $item_id ] ) ) {
				$mashmenu_options = get_post_meta( $item_id , '_mashmenu_options', true );
				if( !$mashmenu_options ) $this->noMashMenu[ $item_id ] = true; //don't check again for this menu item
			}

			//If $mashmenu_options are set, use them
			if( $mashmenu_options ){
				$this->menuItemOptions[ $item_id ] = $mashmenu_options;
			} 
			//Otherwise get the old meta
			else{
				$option_id = '_menu_item_'.$id;
				return get_post_meta( $item_id, $option_id , true );
			}
		}
		return isset( $this->menuItemOptions[ $item_id ][ $option_id ] ) ? stripslashes( $this->menuItemOptions[ $item_id ][ $option_id ] ) : '';
	}
	
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ){
		$args = (object)$args;
		
		// check display logic
		$display_logic = $this->getMashMenuOption( $item->ID, 'displayLogic' );
		if(($display_logic == 'guest' && is_user_logged_in()) || ($display_logic == 'member' && !is_user_logged_in())){
			return;
		}
				
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
 
		//Handle class names depending on menu item settings
		$class_names = $value = '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		if($depth == 1 && $args->parentMega == 'on'){
			$classes[] = 'channel-title';
		}
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="'. esc_attr( $class_names ) . '"';

		$options = get_option('mashmenu_options');
		
		if($depth == 1 && $args->parentMega == 'on'){
			$post_type = 'any';
			/* if you want exactly what kind of post types which belong to this category
			 * uncomment & edit code below
			 * ====================
			 * if($item->object = 'custom-taxonomy') $post_type = 'custom-post-type';
			 * ====================
			 */

			if($options['ajax_loading'] != 'on'){
				$output .= '<div class="channel-content" id="channel-'.$item->ID.'">';
				
				$helper = new MashMenuContentHelper();
				
				switch($item->object){
					case 'category':
						$output .= $helper->getLatestCategoryItems($item->object_id);
						break;
					case 'post_tag':
						$output .= $helper->getLatestItemsByTag($item->object_id);
						break;
					case 'page':
						$output .= $helper->getPageContent($item->object_id);
						break;
					case 'post':
						$output .= $helper->getPostContent($item->object_id);
						break;
					case 'product_cat':
						$output .= $helper->getWProductItems($item->object_id);
						break;
					default:						
						$output .= $helper->getLatestCustomCategoryItems($item->object_id, $item->object,$post_type);
						break;
				}
				
				
				$output .= '</div>';
			}
			
			$output .= /*$indent . */'<li id="menu-item-'. $item->ID . '"' . $value . $class_names .' data-target="channel-'.$item->ID.'" data-type="'.$item->type.'" data-post="'.$post_type.'" data-object="'.$item->object.'" data-id="'.$item->object_id.'">';
		} else if($depth != 1){
			$output .= /*$indent . */'<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
		}

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
		//$attributes .= ! empty( $item->class )      ? ' class="'  . esc_attr( $item->class      ) .'"' : '';
		
		$item_output = '';
		
		/* Add title and normal link content - skip altogether if nolink and notext are both checked */
		if( !empty( $item->title ) && trim( $item->title ) != '' ){
			
			//Determine the title
			$title = apply_filters( 'the_title', $item->title, $item->ID );

			$item_output = $args->before;
			
			if(!in_array("header",$classes)){
				$item_output.= '<a'. $attributes .'>';
			}
			
			$opt_icon = $this->getMashMenuOption( $item->ID, 'icon' );
			$opt_iconPos = $this->getMashMenuOption( $item->ID, 'iconPos' );
			$opt_caretDownPos = $this->getMashMenuOption( $item->ID, 'caretDownPos' );
			
			if($depth == 0 && $opt_caretDownPos == 'left'){
				if($options['icon_mainmenu_parent'] != ''){
					$item_output .= "<i class='fa " . $options['icon_mainmenu_parent'] . "'></i>";
				} else {
					$item_output .= "<i class='fa fa-caret-down'></i>";
				}
			}
			
			if($depth == 1 && $options['rtl'] == 'on'){
				if($options['icon_subchannel_item_left'] != ''){
					$item_output .= "<i class='fa " . $options['icon_subchannel_item_left'] . "'></i>";
				} else {
					$item_output.= '<i class="fa fa-chevron-left"><!-- --></i>';
				}
			}
			
			$item_output.= $args->link_before;
			
			// add menu icon
			if($opt_icon != '' && $opt_iconPos == 'left'){
				$title = "<i class='fa " . $opt_icon . "'></i>" . $title;
			} else if($opt_icon != '' && $opt_iconPos == 'right'){
				$title .= "<i class='fa " . $opt_icon . "'></i>";
			}
			
			//Text - Title
			$item_output.= $prepend . $title . $append;
			
			//Description
			$item_output.= $description;
			
			//Link After 
			$item_output.= $args->link_after;
			
			//Close Link or emulator
			if($depth == 0){
				if($opt_caretDownPos == 'right'){
					if($options['icon_mainmenu_parent'] != ''){
						$item_output .= "<i class='fa " . $options['icon_mainmenu_parent'] . "'></i>";
					} else {
						$item_output .= "<i class='fa fa-caret-down'></i>";
					}
				}
			} else if($depth == 1 && $args->parentMega == 'on' && $options['rtl'] != 'on'){
				if($options['icon_subchannel_item_right'] != ''){
					$item_output .= "<i class='fa " . $options['icon_subchannel_item_right'] . "'></i>";
				} else {
					$item_output.= '<i class="fa fa-chevron-right"><!-- --></i>';
				}
			}
			
			if(!in_array("header",$classes)){
				$item_output.= '</a>';
			}
			
			//Append after Link
			$item_output .= $args->after;
		}
		
		if($depth == 1 && !isset($args->parentMega)){
			$output .= '';
		} else {
			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}

	function end_el(&$output, $item, $depth = 0, $args = array()) {
		$output .= "</li>";
	}
}

class MashMenuWalkerEdit extends Walker_Nav_Menu  {
	
	/**
	 * @see Walker_Nav_Menu::start_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference.
	 */
	function start_lvl(&$output, $depth = 0, $args = array()) {}

	/**
	 * @see Walker_Nav_Menu::end_lvl()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference.
	 */
	function end_lvl(&$output, $depth = 0, $args = array()) {
	}

	/**
	 * @see Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param object $args
	 */
	function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
		global $_wp_nav_menu_max_depth;
		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		ob_start();
		$item_id = esc_attr( $item->ID );
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);

		$original_title = '';
		if ( 'taxonomy' == $item->type ) {
			$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
			if ( is_wp_error( $original_title ) )
				$original_title = false;
		} elseif ( 'post_type' == $item->type ) {
			$original_object = get_post( $item->object_id );
			$original_title = $original_object->post_title;
		}

		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
		);

		$title = $item->title;

		if ( ! empty( $item->_invalid ) ) {
			$classes[] = 'menu-item-invalid';
			/* translators: %s: title of menu item which is invalid */
			$title = sprintf( __( '%s (Invalid)' ), $item->title );
		} elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
			$classes[] = 'pending';
			/* translators: %s: title of menu item in draft status */
			$title = sprintf( __('%s (Pending)'), $item->title );
		}

		$title = empty( $item->label ) ? $title : $item->label;

		?>
		<li id="menu-item-<?php echo $item_id;?>" class="<?php echo implode(' ', $classes);?>">
			<dl class="menu-item-bar">
				<dt class="menu-item-handle">
					<span class="item-title"><?php echo esc_html($title);?></span>
					<span class="item-controls">
						<span class="item-type"><?php echo esc_html($item -> type_label);?></span>
						<span class="item-order hide-if-js">
							<a href="<?php
							echo wp_nonce_url(add_query_arg(array('action' => 'move-up-menu-item', 'menu-item' => $item_id, ), remove_query_arg($removed_args, admin_url('nav-menus.php'))), 'move-menu_item');
							?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up');?>">&#8593;</abbr></a>
							|
							<a href="<?php
							echo wp_nonce_url(add_query_arg(array('action' => 'move-down-menu-item', 'menu-item' => $item_id, ), remove_query_arg($removed_args, admin_url('nav-menus.php'))), 'move-menu_item');
							?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down');?>">&#8595;</abbr></a>
						</span>
						<a class="item-edit" id="edit-<?php echo $item_id;?>" title="<?php esc_attr_e('Edit Menu Item');?>" href="<?php
							echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
						?>"><?php _e('Edit Menu Item');?></a>
					</span>
				</dt>
			</dl>

			<div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id;?>">
				<?php if( 'custom' == $item->type ) : ?>
					<p class="field-url description description-wide">
						<label for="edit-menu-item-url-<?php echo $item_id;?>">
							<?php _e('URL');?><br />
							<input type="text" id="edit-menu-item-url-<?php echo $item_id;?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id;?>]" value="<?php echo esc_attr($item -> url);?>" />
						</label>
					</p>
				<?php endif;?>
				<p class="description description-thin">
					<label for="edit-menu-item-title-<?php echo $item_id;?>">
						<?php _e('Navigation Label');?><br />
						<input type="text" id="edit-menu-item-title-<?php echo $item_id;?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id;?>]" value="<?php echo esc_attr($item -> title);?>" />
					</label>
				</p>
				<p class="description description-thin">
					<label for="edit-menu-item-attr-title-<?php echo $item_id;?>">
						<?php _e('Title Attribute');?><br />
						<input type="text" id="edit-menu-item-attr-title-<?php echo $item_id;?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id;?>]" value="<?php echo esc_attr($item -> post_excerpt);?>" />
					</label>
				</p>
				<p class="field-link-target description">
					<label for="edit-menu-item-target-<?php echo $item_id;?>">
						<input type="checkbox" id="edit-menu-item-target-<?php echo $item_id;?>" value="_blank" name="menu-item-target[<?php echo $item_id;?>]"<?php checked($item -> target, '_blank');?> />
						<?php _e('Open link in a new window/tab');?>
					</label>
				</p>
				<p class="field-css-classes description description-thin">
					<label for="edit-menu-item-classes-<?php echo $item_id;?>">
						<?php _e('CSS Classes (optional)');?><br />
						<input type="text" id="edit-menu-item-classes-<?php echo $item_id;?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id;?>]" value="<?php echo esc_attr(implode(' ', $item -> classes));?>" />
					</label>
				</p>
				<p class="field-xfn description description-thin">
					<label for="edit-menu-item-xfn-<?php echo $item_id;?>">
						<?php _e('Link Relationship (XFN)');?><br />
						<input type="text" id="edit-menu-item-xfn-<?php echo $item_id;?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id;?>]" value="<?php echo esc_attr($item -> xfn);?>" />
					</label>
				</p>
				<p class="field-description description description-wide">
					<label for="edit-menu-item-description-<?php echo $item_id;?>">
						<?php _e('Description');?><br />
						<textarea id="edit-menu-item-description-<?php echo $item_id;?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id;?>]"><?php echo esc_html($item -> description);
							// textarea_escaped
 ?></textarea>
						<span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.');?></span>
					</label>
				</p>
				
				<?php do_action( 'mashmenu_menu_item_options', $item_id );?>

				<div class="menu-item-actions description-wide submitbox">
					<?php if( 'custom' != $item->type && $original_title !== false ) : ?>
						<p class="link-to-original">
							<?php printf(__('Original: %s'), '<a href="' . esc_attr($item -> url) . '">' . esc_html($original_title) . '</a>');?>
						</p>
					<?php endif;?>
					<a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id;?>" href="<?php
					echo wp_nonce_url(add_query_arg(array('action' => 'delete-menu-item', 'menu-item' => $item_id, ), remove_query_arg($removed_args, admin_url('nav-menus.php'))), 'delete-menu_item_' . $item_id);
 ?>"><?php _e('Remove');?></a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-<?php echo $item_id;?>" href="<?php	echo esc_url(add_query_arg(array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg($removed_args, admin_url('nav-menus.php'))));?>#menu-item-settings-<?php echo $item_id;?>"><?php _e('Cancel');?></a>
				</div>

				<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id;?>]" value="<?php echo $item_id;?>" />
				<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id;?>]" value="<?php echo esc_attr($item -> object_id);?>" />
				<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id;?>]" value="<?php echo esc_attr($item -> object);?>" />
				<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id;?>]" value="<?php echo esc_attr($item -> menu_item_parent);?>" />
				<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id;?>]" value="<?php echo esc_attr($item -> menu_order);?>" />
				<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id;?>]" value="<?php echo esc_attr($item -> type);?>" />
			</div><!-- .menu-item-settings-->
			<ul class="menu-item-transport"></ul>
		<?php
		$output .= ob_get_clean();
	}
}