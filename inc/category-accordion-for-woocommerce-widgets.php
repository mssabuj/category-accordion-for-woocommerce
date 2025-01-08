<?php
// Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
    die( "Can't load this file directly" );
}

// Widget Class for Category Accordion
class Tpcafw_Category_Accordion_Widget extends WP_Widget {
    
    // Constructor
    public function __construct() {
        parent::__construct(
            'category_accordion_widget', // Base ID
            __('Category Accordion Widget WooCommerce', 'category-accordion-for-woocommerce'), // Name
            array(
                'description' => __('Displays categories and their subcategories in an accordion style.', 'category-accordion-for-woocommerce'),
            )
        );
    }

    // Widget Frontend
    public function widget($args, $instance) {
        // Extract widget arguments
        // Generate a unique class name based on the widget_id
        $widget_class = 'category-accordion-woo-widget-' . esc_attr($args['widget_id']);

        echo wp_kses_post($args['before_widget']);

        echo '<div class="' . esc_attr($widget_class) . '">';

        // Output title
        $title = apply_filters('widget_title', $instance['title']);

        if (!empty($title)) {
            echo wp_kses_post($args['before_title']) . esc_html($title) . wp_kses_post($args['after_title']);
        }

        // Get widget options
        $hide_empty = !empty($instance['hide_empty']) ? 1 : 0;
        $sort_by = !empty($instance['sort_by']) ? sanitize_text_field($instance['sort_by']) : 'ID'; // Default sorting by ID
        $tp_woo_font_size = !empty($instance['tp_woo_font_size']) ? intval($instance['tp_woo_font_size']) : '16'; // Font size
        $tp_woo_text_color = !empty($instance['tp_woo_text_color']) ? sanitize_hex_color($instance['tp_woo_text_color']) : '#000000'; // Default color
        $tp_woo_link_hover_color = !empty($instance['tp_woo_link_hover_color']) ? sanitize_hex_color($instance['tp_woo_link_hover_color']) : '#0000FF'; // Default color
        $tp_woo_link_active_color = !empty($instance['tp_woo_link_active_color']) ? sanitize_hex_color($instance['tp_woo_link_active_color']) : '#0000FF'; // Default color
        $tp_woo_border_color = !empty($instance['tp_woo_border_color']) ? sanitize_hex_color($instance['tp_woo_border_color']) : '#dddddd'; // Default color

        // Prepare inline styles
        $inline_styles = "
            .$widget_class ul.tp-woo-category-accordion li a {
                color: $tp_woo_text_color;
                font-size: {$tp_woo_font_size}px;
            }
            .$widget_class ul.tp-woo-category-accordion li a:hover {
                color: $tp_woo_link_hover_color;
            }
            .$widget_class ul.tp-woo-category-accordion li.has-subcategories.active a {
                color: $tp_woo_link_active_color;
            }
            .$widget_class ul.tp-woo-category-accordion li {
                border-color: $tp_woo_border_color;
            }
        ";

        // Add inline styles to the already enqueued stylesheet
        wp_add_inline_style('tp-woo-category-accordion', $inline_styles);

        // Query top-level categories
        $top_level_args = array(
            'taxonomy' => 'product_cat',
            'hide_empty' => $hide_empty,
            'parent' => 0, // Fetch only parent categories
            'orderby' => $sort_by,
        );
        $top_level_categories = get_terms($top_level_args);

        // Check if top-level categories are available
        if (!empty($top_level_categories) && !is_wp_error($top_level_categories)) {
            echo '<ul class="tp-woo-category-accordion">';
            foreach ($top_level_categories as $top_level_category) {
                echo $this->display_category_with_children($top_level_category, $hide_empty, $sort_by);
            }
            echo '</ul>';
        } else {
            echo '<p>' . esc_html__('No categories found.', 'category-accordion-for-woocommerce') . '</p>';
        }
        echo '</div>';

        echo wp_kses_post($args['after_widget']);
    }

    // Display category with its children recursively
    private function display_category_with_children($category, $hide_empty, $sort_by) {
        echo '<li><a class="parent-category" href="' . esc_url(get_term_link($category)) . '">' . esc_html($category->name);

        // Check if there are subcategories
        $sub_args = array(
            'taxonomy' => 'product_cat',
            'hide_empty' => $hide_empty,
            'parent' => $category->term_id, // Fetch subcategories of current parent
        );
        $sub_categories = get_terms($sub_args);

        // Output subcategories and add icon if subcategories exist
        if (!empty($sub_categories) && !is_wp_error($sub_categories)) {
            echo '<i class="fa fa-chevron-right"></i>';
            echo '</a>';
            echo '<ul class="sub-categories">';
            foreach ($sub_categories as $sub_category) {
                echo $this->display_category_with_children($sub_category, $hide_empty, $sort_by);
            }
            echo '</ul>';
        } else {
            echo '</a>';
        }

        echo '</li>';
    }

    // Widget Backend
    public function form($instance) {
        // Widget form code here
        $title = !empty($instance['title']) ? $instance['title'] : __('Category Accordion', 'category-accordion-for-woocommerce');
        $hide_empty = !empty($instance['hide_empty']) ? $instance['hide_empty'] : false;
        $sort_by = !empty($instance['sort_by']) ? $instance['sort_by'] : 'ID'; // Default sorting by ID
        $tp_woo_font_size = !empty($instance['tp_woo_font_size']) ? $instance['tp_woo_font_size'] : '16'; // Font size
        $tp_woo_text_color = !empty($instance['tp_woo_text_color']) ? $instance['tp_woo_text_color'] : '#000000'; // Default color
        $tp_woo_link_hover_color = !empty($instance['tp_woo_link_hover_color']) ? $instance['tp_woo_link_hover_color'] : '#0000FF'; // Default color
        $tp_woo_link_active_color = !empty($instance['tp_woo_link_active_color']) ? $instance['tp_woo_link_active_color'] : '#0000FF'; // Default color
        $tp_woo_border_color = !empty($instance['tp_woo_border_color']) ? $instance['tp_woo_border_color'] : '#dddddd'; // Default color
        ?>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'category-accordion-for-woocommerce'); ?></label> 
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>

        <p>
            <input id="<?php echo esc_attr($this->get_field_id('hide_empty')); ?>" name="<?php echo esc_attr($this->get_field_name('hide_empty')); ?>" type="checkbox" value="1" <?php checked($hide_empty, 1); ?> />
            <label for="<?php echo esc_attr($this->get_field_id('hide_empty')); ?>"><?php esc_html_e('Hide Empty Categories', 'category-accordion-for-woocommerce'); ?></label>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('sort_by')); ?>"><?php esc_html_e('Sort By:', 'category-accordion-for-woocommerce'); ?></label>
            <select id="<?php echo esc_attr($this->get_field_id('sort_by')); ?>" name="<?php echo esc_attr($this->get_field_name('sort_by')); ?>">
                <option value="ID" <?php selected($sort_by, 'ID'); ?>><?php esc_html_e('ID', 'category-accordion-for-woocommerce'); ?></option>
                <option value="name" <?php selected($sort_by, 'name'); ?>><?php esc_html_e('Name', 'category-accordion-for-woocommerce'); ?></option>
                <option value="slug" <?php selected($sort_by, 'slug'); ?>><?php esc_html_e('Slug', 'category-accordion-for-woocommerce'); ?></option>
            </select>
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('tp_woo_text_color')); ?>"><?php esc_html_e('Text Color:', 'category-accordion-for-woocommerce'); ?></label>
            <input type="text" name="<?php echo esc_attr($this->get_field_name('tp_woo_text_color')); ?>" class="color-picker" id="<?php echo esc_attr($this->get_field_id('tp_woo_text_color')); ?>" value="<?php echo esc_attr($tp_woo_text_color); ?>" />
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('tp_woo_font_size')); ?>"><?php esc_html_e('Font Size (px):', 'category-accordion-for-woocommerce'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('tp_woo_font_size')); ?>" name="<?php echo esc_attr($this->get_field_name('tp_woo_font_size')); ?>" type="number" value="<?php echo esc_attr($tp_woo_font_size); ?>" />
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('tp_woo_link_hover_color')); ?>"><?php esc_html_e('Link Hover Color:', 'category-accordion-for-woocommerce'); ?></label>
            <input type="text" name="<?php echo esc_attr($this->get_field_name('tp_woo_link_hover_color')); ?>" class="color-picker" id="<?php echo esc_attr($this->get_field_id('tp_woo_link_hover_color')); ?>" value="<?php echo esc_attr($tp_woo_link_hover_color); ?>" />
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('tp_woo_link_active_color')); ?>"><?php esc_html_e('Active Link Color:', 'category-accordion-for-woocommerce'); ?></label>
            <input type="text" name="<?php echo esc_attr($this->get_field_name('tp_woo_link_active_color')); ?>" class="color-picker" id="<?php echo esc_attr($this->get_field_id('tp_woo_link_active_color')); ?>" value="<?php echo esc_attr($tp_woo_link_active_color); ?>" />
        </p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('tp_woo_border_color')); ?>"><?php esc_html_e('Border Color:', 'category-accordion-for-woocommerce'); ?></label>
            <input type="text" name="<?php echo esc_attr($this->get_field_name('tp_woo_border_color')); ?>" class="color-picker" id="<?php echo esc_attr($this->get_field_id('tp_woo_border_color')); ?>" value="<?php echo esc_attr($tp_woo_border_color); ?>" />
        </p>

        <?php
    }

    // Save Widget Settings
    public function update($new_instance, $old_instance) {
        // Update widget settings here
        $instance = $old_instance;
        $instance['title'] = !empty($new_instance['title']) ? wp_strip_all_tags($new_instance['title']) : __('Accordion', 'category-accordion-for-woocommerce');
        $instance['hide_empty'] = !empty($new_instance['hide_empty']) ? 1 : 0;
        $instance['sort_by'] = !empty($new_instance['sort_by']) ? sanitize_text_field($new_instance['sort_by']) : 'ID'; // Default sorting by ID
        $instance['tp_woo_text_color'] = !empty($new_instance['tp_woo_text_color']) ? sanitize_hex_color($new_instance['tp_woo_text_color']) : '#000000'; // Default color
        $instance['tp_woo_font_size'] = !empty($new_instance['tp_woo_font_size']) ? absint($new_instance['tp_woo_font_size']) : ''; // Font size
        $instance['tp_woo_link_hover_color'] = !empty($new_instance['tp_woo_link_hover_color']) ? sanitize_hex_color($new_instance['tp_woo_link_hover_color']) : '#0000FF';
        $instance['tp_woo_link_active_color'] = !empty($new_instance['tp_woo_link_active_color']) ? sanitize_hex_color($new_instance['tp_woo_link_active_color']) : '#0000FF';
        $instance['tp_woo_border_color'] = !empty($new_instance['tp_woo_border_color']) ? sanitize_hex_color($new_instance['tp_woo_border_color']) : '#dddddd';
        return $instance;
    }
}

// Register the widget
function tpcafw_accordion_widget() {
    register_widget('Tpcafw_Category_Accordion_Widget');
}
add_action('widgets_init', 'tpcafw_accordion_widget');