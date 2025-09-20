<?php

/**
 * Enregistrement des blocs ACF personnalisés
 */

// Enregistrement des blocs ACF
add_action('acf/init', 'register_acf_blocks');

function register_acf_blocks() {
    // Vérifier si ACF est disponible
    if (function_exists('acf_register_block_type')) {
        
        // Bloc pour afficher les ressources
        acf_register_block_type(array(
            'name'              => 'ressource-display',
            'title'             => __('Affichage Ressource'),
            'description'       => __('Bloc pour afficher tous les champs d\'une ressource'),
            'render_template'   => 'blocks/card-resource.php',
            'category'          => 'formatting',
            'icon'              => 'admin-page',
            'keywords'          => array('ressource', 'resource', 'champs'),
            'supports'          => array(
                'align' => true,
                'mode' => false,
                'jsx' => true,
            ),
            'example' => array(
                'attributes' => array(
                    'mode' => 'preview',
                    'data' => array(
                        'preview_image_help' => get_template_directory_uri() . '/src/images/1x/app-photo.png',
                    )
                )
            )
        ));
    }
}

// Créer les champs ACF pour le bloc ressource
add_action('acf/init', 'create_ressource_block_fields');

function create_ressource_block_fields() {
    if (function_exists('acf_add_local_field_group')) {
        
        acf_add_local_field_group(array(
            'key' => 'group_ressource_display_block',
            'title' => 'Bloc Affichage Ressource',
            'fields' => array(
                array(
                    'key' => 'field_ressource_id',
                    'label' => 'Sélectionner une ressource',
                    'name' => 'ressource_id',
                    'type' => 'post_object',
                    'instructions' => 'Choisissez la ressource à afficher. Si aucune ressource n\'est sélectionnée, la ressource courante sera utilisée.',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'post_type' => array(
                        0 => 'ressource',
                    ),
                    'taxonomy' => '',
                    'allow_null' => 1,
                    'multiple' => 0,
                    'return_format' => 'id',
                    'ui' => 1,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'block',
                        'operator' => '==',
                        'value' => 'acf/ressource-display',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => 'Champs pour le bloc d\'affichage de ressource',
        ));
    }
}
