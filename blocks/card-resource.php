<?php

/**
 * Template pour le bloc Ressource Display
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

?>

<div class="<?php echo esc_attr($className); ?>">
    <div class="ressource-display__container">

        <!-- Titre de la ressource -->
        <!-- <header class="ressource-display__header">
            <h2 class="ressource-display__title"><?php echo esc_html($post->post_title); ?></h2>
        </header> -->

        <div class="ressource-display__content">

            <!-- Champs principaux -->
            <div class="ressource-display__main-fields">

                <?php if (isset($fields['titre']) && !empty($fields['titre'])): ?>
                    <div class="ressource-display__field ressource-display__field--titre">
                        <h3 class="ressource-display__field-value"><?php echo esc_html($fields['titre']); ?></h3>
                    </div>
                <?php endif; ?>

                <?php if (isset($fields['auteur']) && !empty($fields['auteur'])): ?>
                    <div class="ressource-display__field ressource-display__field--auteur">
                        <span class="ressource-display__field-value"><?php echo esc_html($fields['auteur']); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (isset($fields['annee_parution']) && !empty($fields['annee_parution'])): ?>
                    <div class="ressource-display__field ressource-display__field--annee">
                        <span class="ressource-display__field-value"><?php echo esc_html($fields['annee_parution']); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (isset($fields['fichier']) && !empty($fields['fichier'])): ?>
                    <div class="ressource-display__field ressource-display__field--fichier">
                        <div class="ressource-display__field-value">
                            <?php if (is_array($fields['fichier'])): ?>
                                <a href="<?php echo esc_url($fields['fichier']['url']); ?>"
                                    target="_blank"
                                    class="ressource-display__file-link">
                                    <?php echo esc_html($fields['fichier']['title'] ?: $fields['fichier']['filename']); ?>
                                    <span class="ressource-display__file-size">
                                        (<?php echo size_format($fields['fichier']['filesize']); ?>)
                                    </span>
                                </a>
                            <?php else: ?>
                                <a href="<?php echo esc_url($fields['fichier']); ?>"
                                    target="_blank"
                                    class="ressource-display__file-link">
                                    Télécharger le fichier
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php 
                // Récupérer l'extrait de la ressource
                $excerpt = get_the_excerpt($selected_post_id);
                if (!empty($excerpt)): ?>
                    <div class="ressource-display__field ressource-display__field--excerpt">
                        <div class="ressource-display__field-value ressource-display__excerpt">
                            <p><?php echo wp_kses_post($excerpt); ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($fields['critique']) && !empty($fields['critique'])): ?>
                    <div class="ressource-display__field ressource-display__field--critique">
                        <div class="ressource-display__field-value ressource-display__field-value--textarea">
                            <?php echo wp_kses_post($fields['critique']); ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Affichage de tous les autres champs personnalisés -->
            <?php
            $displayed_fields = array('titre', 'auteur', 'annee_parution', 'fichier', 'critique');
            $other_fields = array_diff_key($fields, array_flip($displayed_fields));

            if (!empty($other_fields)): ?>
                <div class="ressource-display__additional-fields">
                    <h3 class="ressource-display__additional-title">Informations complémentaires</h3>

                    <?php foreach ($other_fields as $field_name => $field_value): ?>
                        <?php if (!empty($field_value)): ?>
                            <div class="ressource-display__field ressource-display__field--<?php echo esc_attr($field_name); ?>">
                                <span class="ressource-display__field-label">
                                    <?php echo esc_html(ucfirst(str_replace('_', ' ', $field_name))); ?> :
                                </span>
                                <div class="ressource-display__field-value">
                                    <?php
                                    // Gestion des différents types de champs
                                    if (is_array($field_value)) {
                                        if (isset($field_value['url'])) {
                                            // Champ fichier/image
                                            echo '<a href="' . esc_url($field_value['url']) . '" target="_blank">' .
                                                esc_html($field_value['title'] ?: $field_value['filename']) . '</a>';
                                        } else {
                                            // Autre type de tableau
                                            echo esc_html(implode(', ', array_filter($field_value)));
                                        }
                                    } elseif (filter_var($field_value, FILTER_VALIDATE_URL)) {
                                        // URL
                                        echo '<a href="' . esc_url($field_value) . '" target="_blank">' . esc_html($field_value) . '</a>';
                                    } else {
                                        // Texte simple
                                        echo wp_kses_post($field_value);
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>