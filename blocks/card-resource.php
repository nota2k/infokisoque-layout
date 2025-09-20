<?php

/**
 * Template pour le bloc Ressource Display avec InnerBlocks
 * 
 * @var array $block Les paramètres du bloc
 * @var string $content Le contenu du bloc
 * @var bool $is_preview Si on est en mode preview
 * @var int $post_id L'ID du post
 */

// Récupérer l'ID du post sélectionné ou utiliser le post courant
$selected_post_id = get_field('ressource_id') ?: get_the_ID();

// Vérifier si c'est un post de type ressource
if (get_post_type($selected_post_id) !== 'ressource') {
    if ($is_preview) {
        echo '<p>Veuillez sélectionner une ressource valide.</p>';
        return;
    }
    return;
}

// Récupérer tous les champs ACF de la ressource
$fields = get_fields($selected_post_id);
$post = get_post($selected_post_id);

if (!$post || !$fields) {
    if ($is_preview) {
        echo '<p>Aucune donnée trouvée pour cette ressource.</p>';
        return;
    }
    return;
}

// Classes CSS pour le bloc
$className = 'ressource-display';
if (!empty($block['className'])) {
    $className .= ' ' . $block['className'];
}
if (!empty($block['align'])) {
    $className .= ' align' . $block['align'];
}

// Préparer les données pour les blocs
$titre = isset($fields['titre']) ? $fields['titre'] : '';
$auteur = isset($fields['auteur']) ? $fields['auteur'] : '';
$annee = isset($fields['annee_parution']) ? $fields['annee_parution'] : '';
$fichier = isset($fields['fichier']) ? $fields['fichier'] : '';
$critique = isset($fields['critique']) ? $fields['critique'] : '';
$excerpt = get_the_excerpt($selected_post_id);

// Template pour les InnerBlocks
$template = array();

// Titre
if (!empty($titre)) {
    $template[] = array('core/heading', array(
        'level' => 3,
        'content' => $titre,
        'className' => 'ressource-display__titre'
    ));
}

// Auteur et année sur la même ligne
if (!empty($auteur) || !empty($annee)) {
    $author_year_content = '';
    if (!empty($auteur)) {
        $author_year_content .= '<strong>' . esc_html($auteur) . '</strong>';
    }
    if (!empty($annee)) {
        if (!empty($auteur)) {
            $author_year_content .= ' • ';
        }
        $author_year_content .= esc_html($annee);
    }
    
    $template[] = array('core/paragraph', array(
        'content' => $author_year_content,
        'className' => 'ressource-display__meta'
    ));
}

// Extrait
if (!empty($excerpt)) {
    $template[] = array('core/paragraph', array(
        'content' => $excerpt,
        'className' => 'ressource-display__excerpt'
    ));
}

// Fichier (bouton de téléchargement)
if (!empty($fichier)) {
    $file_url = is_array($fichier) ? $fichier['url'] : $fichier;
    $file_title = is_array($fichier) ? ($fichier['title'] ?: $fichier['filename']) : 'Télécharger le fichier';
    $file_size = is_array($fichier) && isset($fichier['filesize']) ? ' (' . size_format($fichier['filesize']) . ')' : '';
    
    $template[] = array('core/buttons', array(
        'className' => 'ressource-display__download'
    ), array(
        array('core/button', array(
            'text' => $file_title . $file_size,
            'url' => $file_url,
            'linkTarget' => '_blank',
            'className' => 'is-style-outline ressource-display__download-btn'
        ))
    ));
}

// Critique
if (!empty($critique)) {
    $template[] = array('core/paragraph', array(
        'content' => $critique,
        'className' => 'ressource-display__critique'
    ));
}

// Autres champs
$displayed_fields = array('titre', 'auteur', 'annee_parution', 'fichier', 'critique');
$other_fields = array_diff_key($fields, array_flip($displayed_fields));

if (!empty($other_fields)) {
    $template[] = array('core/heading', array(
        'level' => 4,
        'content' => 'Informations complémentaires',
        'className' => 'ressource-display__additional-title'
    ));
    
    foreach ($other_fields as $field_name => $field_value) {
        if (!empty($field_value)) {
            $field_label = ucfirst(str_replace('_', ' ', $field_name));
            $field_content = '';
            
            // Gestion des différents types de champs
            if (is_array($field_value)) {
                if (isset($field_value['url'])) {
                    // Champ fichier/image
                    $field_content = '<a href="' . esc_url($field_value['url']) . '" target="_blank">' .
                        esc_html($field_value['title'] ?: $field_value['filename']) . '</a>';
                } else {
                    // Autre type de tableau
                    $field_content = esc_html(implode(', ', array_filter($field_value)));
                }
            } elseif (filter_var($field_value, FILTER_VALIDATE_URL)) {
                // URL
                $field_content = '<a href="' . esc_url($field_value) . '" target="_blank">' . esc_html($field_value) . '</a>';
            } else {
                // Texte simple
                $field_content = wp_kses_post($field_value);
            }
            
            $template[] = array('core/paragraph', array(
                'content' => '<strong>' . esc_html($field_label) . ' :</strong> ' . $field_content,
                'className' => 'ressource-display__field ressource-display__field--' . esc_attr($field_name)
            ));
        }
    }
}

?>

<div class="<?php echo esc_attr($className); ?>">
    <div class="ressource-display__container">
        <div class="ressource-display__content">
            <?php
            // Générer les blocs natifs en HTML avec les classes appropriées
            foreach ($template as $block) {
                $block_type = $block[0];
                $block_attrs = isset($block[1]) ? $block[1] : array();
                $inner_blocks = isset($block[2]) ? $block[2] : array();
                
                switch ($block_type) {
                    case 'core/heading':
                        $level = isset($block_attrs['level']) ? $block_attrs['level'] : 2;
                        $content = isset($block_attrs['content']) ? $block_attrs['content'] : '';
                        $class = isset($block_attrs['className']) ? $block_attrs['className'] : '';
                        echo '<h' . $level . ' class="wp-block-heading ' . esc_attr($class) . '">' . wp_kses_post($content) . '</h' . $level . '>';
                        break;
                        
                    case 'core/paragraph':
                        $content = isset($block_attrs['content']) ? $block_attrs['content'] : '';
                        $class = isset($block_attrs['className']) ? $block_attrs['className'] : '';
                        echo '<p class="wp-block-paragraph ' . esc_attr($class) . '">' . wp_kses_post($content) . '</p>';
                        break;
                        
                    case 'core/buttons':
                        $class = isset($block_attrs['className']) ? $block_attrs['className'] : '';
                        echo '<div class="wp-block-buttons ' . esc_attr($class) . '">';
                        
                        foreach ($inner_blocks as $button_block) {
                            if ($button_block[0] === 'core/button') {
                                $btn_attrs = isset($button_block[1]) ? $button_block[1] : array();
                                $text = isset($btn_attrs['text']) ? $btn_attrs['text'] : '';
                                $url = isset($btn_attrs['url']) ? $btn_attrs['url'] : '';
                                $target = isset($btn_attrs['linkTarget']) ? $btn_attrs['linkTarget'] : '';
                                $btn_class = isset($btn_attrs['className']) ? $btn_attrs['className'] : '';
                                
                                echo '<div class="wp-block-button ' . esc_attr($btn_class) . '">';
                                echo '<a class="wp-block-button__link" href="' . esc_url($url) . '"';
                                if ($target) echo ' target="' . esc_attr($target) . '"';
                                echo '>' . esc_html($text) . '</a>';
                                echo '</div>';
                            }
                        }
                        
                        echo '</div>';
                        break;
                }
            }
            ?>
        </div>
    </div>
</div>