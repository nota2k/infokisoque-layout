<?php
/**
 * Template pour le bloc Card Resource Éditable
 * 
 * @var array $block Les paramètres du bloc
 * @var string $content Le contenu du bloc (InnerBlocks)
 * @var bool $is_preview Si on est en mode preview
 * @var int $post_id L'ID du post
 */

// Récupérer l'ID de la ressource depuis les attributs du bloc
$ressource_id = 0;
if (isset($block_attributes['ressourceId'])) {
    $ressource_id = intval($block_attributes['ressourceId']);
}

// Classes CSS pour le bloc
$className = 'ressource-display';
if (!empty($block_attributes['className'])) {
    $className .= ' ' . $block_attributes['className'];
}
if (!empty($block_attributes['align'])) {
    $className .= ' align' . $block_attributes['align'];
}

?>

<div class="<?php echo esc_attr($className); ?>">
    <div class="ressource-display__container">
        <div class="ressource-display__content">
            
            <?php if ($ressource_id && get_post_type($ressource_id) === 'ressource'): ?>
                <?php 
                // Récupérer les données de la ressource
                $ressource_post = get_post($ressource_id);
                $acf_fields = get_fields($ressource_id);
                ?>
                
                <!-- Données de la ressource -->
                <div class="ressource-display__resource-data">
                    
                    <!-- Titre -->
                    <?php 
                    $titre = isset($acf_fields['titre']) ? $acf_fields['titre'] : $ressource_post->post_title;
                    if ($titre): ?>
                        <h3 class="wp-block-heading ressource-display__titre">
                            <?php echo esc_html($titre); ?>
                        </h3>
                    <?php endif; ?>
                    
                    <!-- Métadonnées -->
                    <?php if ((isset($acf_fields['auteur']) && !empty($acf_fields['auteur'])) || 
                              (isset($acf_fields['annee_parution']) && !empty($acf_fields['annee_parution']))): ?>
                        <p class="wp-block-paragraph ressource-display__meta">
                            <?php if (isset($acf_fields['auteur']) && !empty($acf_fields['auteur'])): ?>
                                <strong><?php echo esc_html($acf_fields['auteur']); ?></strong>
                            <?php endif; ?>
                            <?php if (isset($acf_fields['auteur']) && !empty($acf_fields['auteur']) && 
                                      isset($acf_fields['annee_parution']) && !empty($acf_fields['annee_parution'])): ?>
                                 • 
                            <?php endif; ?>
                            <?php if (isset($acf_fields['annee_parution']) && !empty($acf_fields['annee_parution'])): ?>
                                <?php echo esc_html($acf_fields['annee_parution']); ?>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                    
                    <!-- Extrait -->
                    <?php 
                    $excerpt = get_the_excerpt($ressource_id);
                    if (!empty($excerpt)): ?>
                        <div class="wp-block-paragraph ressource-display__excerpt">
                            <?php echo wp_kses_post($excerpt); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Bouton de téléchargement -->
                    <?php if (isset($acf_fields['fichier']) && !empty($acf_fields['fichier'])): ?>
                        <div class="wp-block-buttons ressource-display__download">
                            <div class="wp-block-button ressource-display__download-btn">
                                <?php 
                                $file_url = is_array($acf_fields['fichier']) ? $acf_fields['fichier']['url'] : $acf_fields['fichier'];
                                $file_title = is_array($acf_fields['fichier']) ? 
                                    ($acf_fields['fichier']['title'] ?: $acf_fields['fichier']['filename']) : 
                                    'Télécharger le fichier';
                                ?>
                                <a class="wp-block-button__link" 
                                   href="<?php echo esc_url($file_url); ?>" 
                                   target="_blank">
                                    <?php echo esc_html($file_title); ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Critique -->
                    <?php if (isset($acf_fields['critique']) && !empty($acf_fields['critique'])): ?>
                        <div class="wp-block-paragraph ressource-display__critique">
                            <?php echo wp_kses_post($acf_fields['critique']); ?>
                        </div>
                    <?php endif; ?>
                    
                </div>
                
                <!-- Contenu personnalisable (InnerBlocks) -->
                <div class="ressource-display__custom-content">
                    <?php echo $content; ?>
                </div>
                
            <?php else: ?>
                <!-- Pas de ressource sélectionnée ou template par défaut -->
                <div class="ressource-display__default-content">
                    <?php echo $content; ?>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</div>
