/**
 * Bloc Card Resource Simple - Approche directe
 */

const { registerBlockType } = wp.blocks;
const { InspectorControls, RichText } = wp.blockEditor;
const { PanelBody, SelectControl } = wp.components;
const { useSelect } = wp.data;
const { useState, useEffect } = wp.element;
const { __ } = wp.i18n;

registerBlockType('infokiosque/card-resource-simple', {
    title: __('Card Resource Simple', 'infokiosque'),
    description: __('Affiche une ressource avec des éléments éditables (approche simple)', 'infokiosque'),
    icon: 'id-alt',
    category: 'widgets',
    
    attributes: {
        ressourceId: {
            type: 'number',
            default: 0
        },
        titre: {
            type: 'string',
            default: ''
        },
        auteur: {
            type: 'string',
            default: ''
        },
        annee: {
            type: 'string',
            default: ''
        },
        excerpt: {
            type: 'string',
            default: ''
        },
        critique: {
            type: 'string',
            default: ''
        },
        fichierUrl: {
            type: 'string',
            default: ''
        },
        fichierTitle: {
            type: 'string',
            default: 'Télécharger le fichier'
        }
    },

    supports: {
        align: true,
        anchor: true,
        customClassName: true,
        spacing: {
            margin: true,
            padding: true
        },
        typography: {
            fontSize: true,
            lineHeight: true
        },
        color: {
            background: true,
            text: true
        }
    },

    edit: function(props) {
        const { attributes, setAttributes } = props;
        const { ressourceId, titre, auteur, annee, excerpt, critique, fichierUrl, fichierTitle } = attributes;

        // Récupérer la liste des ressources
        const ressources = useSelect((select) => {
            return select('core').getEntityRecords('postType', 'ressource', {
                per_page: -1,
                status: 'publish'
            });
        }, []);

        // Récupérer les données de la ressource sélectionnée
        const selectedRessource = useSelect((select) => {
            if (!ressourceId) return null;
            return select('core').getEntityRecord('postType', 'ressource', ressourceId);
        }, [ressourceId]);

        // Récupérer les champs ACF de la ressource sélectionnée
        const acfFields = useSelect((select) => {
            if (!ressourceId) return null;
            return select('core').getEntityRecord('postType', 'ressource', ressourceId, {
                context: 'edit'
            });
        }, [ressourceId]);

        const ressourceOptions = ressources ? [
            { label: 'Sélectionner une ressource...', value: 0 },
            ...ressources.map(ressource => ({
                label: ressource.title.rendered,
                value: ressource.id
            }))
        ] : [{ label: 'Chargement...', value: 0 }];

        // Fonction pour nettoyer le HTML
        const cleanContent = (content) => {
            if (!content) return '';
            return content.replace(/<br\s*\/?>/gi, ' ').replace(/\s+/g, ' ').trim();
        };

        // Auto-remplir les attributs quand une ressource est sélectionnée
        useEffect(() => {
            if (ressourceId && selectedRessource && acfFields) {
                const newTitre = acfFields.acf?.titre || selectedRessource.title?.rendered || '';
                const newAuteur = acfFields.acf?.auteur || '';
                const newAnnee = acfFields.acf?.annee_parution || '';
                const newExcerpt = selectedRessource.excerpt?.rendered ? cleanContent(selectedRessource.excerpt.rendered) : '';
                const newCritique = acfFields.acf?.critique ? cleanContent(acfFields.acf.critique) : '';
                
                let newFichierUrl = '';
                let newFichierTitle = 'Télécharger le fichier';
                
                if (acfFields.acf?.fichier) {
                    const fichier = acfFields.acf.fichier;
                    newFichierUrl = typeof fichier === 'object' ? fichier.url : fichier;
                    newFichierTitle = typeof fichier === 'object' ? 
                        (fichier.title || fichier.filename || 'Télécharger le fichier') : 
                        'Télécharger le fichier';
                }

                setAttributes({
                    titre: newTitre,
                    auteur: newAuteur,
                    annee: newAnnee,
                    excerpt: newExcerpt,
                    critique: newCritique,
                    fichierUrl: newFichierUrl,
                    fichierTitle: newFichierTitle
                });
            }
        }, [ressourceId, selectedRessource, acfFields]);

        return (
            <>
                <InspectorControls>
                    <PanelBody title={__('Paramètres de la ressource', 'infokiosque')}>
                        <SelectControl
                            label={__('Choisir une ressource', 'infokiosque')}
                            value={ressourceId}
                            options={ressourceOptions}
                            onChange={(value) => setAttributes({ ressourceId: parseInt(value) })}
                        />
                    </PanelBody>
                </InspectorControls>

                <div className="ressource-display">
                    <div className="ressource-display__container">
                        <div className="ressource-display__content">
                            
                            {/* Titre éditable */}
                            <RichText
                                tagName="h3"
                                className="wp-block-heading ressource-display__titre"
                                value={titre}
                                onChange={(value) => setAttributes({ titre: value })}
                                placeholder="Titre de la ressource..."
                                allowedFormats={['core/bold', 'core/italic']}
                            />

                            {/* Métadonnées éditables */}
                            {(auteur || annee) && (
                                <p className="wp-block-paragraph ressource-display__meta" style={{fontSize: '0.9rem', color: '#666', marginBottom: '1rem'}}>
                                    {auteur && <strong>{auteur}</strong>}
                                    {auteur && annee && ' • '}
                                    {annee && annee}
                                </p>
                            )}

                            {/* Extrait éditable */}
                            <RichText
                                tagName="p"
                                className="wp-block-paragraph ressource-display__excerpt"
                                value={excerpt}
                                onChange={(value) => setAttributes({ excerpt: value })}
                                placeholder="Extrait de la ressource..."
                                style={{fontStyle: 'italic', padding: '0.75rem', margin: '1rem 0'}}
                            />

                            {/* Bouton de téléchargement éditable */}
                            {fichierUrl && (
                                <div className="wp-block-buttons ressource-display__download" style={{margin: '1rem 0'}}>
                                    <div className="wp-block-button ressource-display__download-btn">
                                        <RichText
                                            tagName="a"
                                            className="wp-block-button__link"
                                            value={fichierTitle}
                                            onChange={(value) => setAttributes({ fichierTitle: value })}
                                            href={fichierUrl}
                                            target="_blank"
                                            style={{
                                                border: '2px solid #007cba',
                                                color: '#007cba',
                                                background: 'transparent',
                                                textDecoration: 'none'
                                            }}
                                        />
                                    </div>
                                </div>
                            )}

                            {/* Critique éditable */}
                            <RichText
                                tagName="p"
                                className="wp-block-paragraph ressource-display__critique"
                                value={critique}
                                onChange={(value) => setAttributes({ critique: value })}
                                placeholder="Critique ou description..."
                                style={{lineHeight: '1.6', marginTop: '1rem'}}
                            />

                        </div>
                    </div>
                </div>
            </>
        );
    },

    save: function(props) {
        const { titre, auteur, annee, excerpt, critique, fichierUrl, fichierTitle } = props.attributes;

        return (
            <div className="ressource-display">
                <div className="ressource-display__container">
                    <div className="ressource-display__content">
                        
                        {/* Titre */}
                        {titre && (
                            <RichText.Content
                                tagName="h3"
                                className="wp-block-heading ressource-display__titre"
                                value={titre}
                            />
                        )}

                        {/* Métadonnées */}
                        {(auteur || annee) && (
                            <p className="wp-block-paragraph ressource-display__meta">
                                {auteur && <strong>{auteur}</strong>}
                                {auteur && annee && ' • '}
                                {annee && annee}
                            </p>
                        )}

                        {/* Extrait */}
                        {excerpt && (
                            <RichText.Content
                                tagName="p"
                                className="wp-block-paragraph ressource-display__excerpt"
                                value={excerpt}
                            />
                        )}

                        {/* Bouton de téléchargement */}
                        {fichierUrl && (
                            <div className="wp-block-buttons ressource-display__download">
                                <div className="wp-block-button ressource-display__download-btn">
                                    <RichText.Content
                                        tagName="a"
                                        className="wp-block-button__link"
                                        value={fichierTitle}
                                        href={fichierUrl}
                                        target="_blank"
                                    />
                                </div>
                            </div>
                        )}

                        {/* Critique */}
                        {critique && (
                            <RichText.Content
                                tagName="p"
                                className="wp-block-paragraph ressource-display__critique"
                                value={critique}
                            />
                        )}

                    </div>
                </div>
            </div>
        );
    }
});
