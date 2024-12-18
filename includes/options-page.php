<?php 

if( !defined('ABSPATH') )
{
      die('You cannot be here');
}

use Carbon_Fields\Field;
use Carbon_Fields\Container;



add_action('after_setup_theme', 'load_carbon_fields');
add_action('carbon_fields_register_fields', 'create_options_page');

function load_carbon_fields()
{
   \Carbon_Fields\Carbon_Fields::boot();
}

function create_options_page() {
    Container::make( 'theme_options', __( 'MailBaby Settings' ) )
        ->set_page_menu_position(30)
        ->set_icon('dashicons-reddit')
        ->add_tab( __( 'Confirmation Email Settings' ), array(
            Field::make( 'checkbox', 'mailbaby_plugin_active', __( 'Is Active' ) )
                ->set_option_value( 'yes' )
                ->set_default_value( 'yes' ),

            Field::make( 'text', 'mailbaby_plugin_recipients', __( 'Recipients Email' ) )
                ->set_attribute( 'placeholder', 'e.g. email@email.com' )
                ->set_help_text('Type the email you want the submissions to go to.'),

            Field::make( 'textarea', 'mailbaby_plugin_message', __( 'Confirmation Message' ) )
                ->set_attribute( 'placeholder', 'e.g. Enter Confirmation Message' )
                ->set_help_text('Type the message you would like the user to see.'),
        ) )
        ->add_tab( __( 'Form Appearance Settings' ), array(
            // Field::make( 'image', 'mailbaby_plugin_image_src', __( 'Image' ) )
            // ->set_value_type( 'url' ),

            Field::make( 'text', 'mailbaby_plugin_heading', __( 'Heading' ) )
                ->set_attribute( 'placeholder', 'Enter Heading Text' ),

            Field::make( 'text', 'mailbaby_plugin_subheading', __( 'Subheading' ) )
                ->set_attribute( 'placeholder', 'Enter Subheading Text' ),
        ) );
}


