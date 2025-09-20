/**
 * Bloc Card Resource avec injection automatique des données ACF
 */

const { registerBlockType } = wp.blocks;
const { InnerBlocks, InspectorControls } = wp.blockEditor;
const { PanelBody, SelectControl } = wp.components;
const { useSelect } = wp.data;
const { __ } = wp.i18n;

// Template par défaut (vide au départ)
const DEFAULT_TEMPLATE = [
    ['core/heading', { 
        level: 3, 
        placeholder: 'Titre de la ressource...',
        className: 'ressource-display__titre'
    }],
    ['core/paragraph', { 
        placeholder: 'Auteur • Année...',
        className: 'ressource-display__meta'
    }],
    ['core/paragraph', { 
        placeholder: 'Extrait de la ressource...',
        className: 'ressource-display__excerpt'
    }],
    ['core/buttons', { 
        className: 'ressource-display__download'
    }, [
        ['core/button', {
            text: 'Télécharger le fichier',
            className: 'is-style-outline ressource-display__download-btn'
        }]
    ]],
    ['core/paragraph', { 
        placeholder: 'Critique ou description...',
        className: 'ressource-display__critique'
    }]
];

registerBlockType('infokiosque/card-resource-editable', {
    title: __('Card Resource Éditable', 'infokiosque'),
    description: __('Affiche une ressource avec des éléments éditables', 'infokiosque'),
    icon: 'id-alt',
    category: 'widgets',
    
    attributes: {
        ressourceId: {
            type: 'number',
            default: 0
        }
    },

    supports: {
        // Activer les supports pour permettre la personnalisation
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
        const { ressourceId } = attributes;

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

        // Fonction pour nettoyer le HTML (supprimer les <br> uniquement)
        const cleanContent = (content) => {
            if (!content) return '';
            return content.replace(/<br\s*\/?>/gi, ' ').replace(/\s+/g, ' ').trim();
        };

        // Créer un template dynamique avec les données
        const createDynamicTemplate = () => {
            if (!ressourceId || !selectedRessource || !acfFields) {
                return DEFAULT_TEMPLATE;
            }

            const template = [];
            
            // Titre
            const titre = acfFields.acf?.titre || selectedRessource.title?.rendered || '';
            if (titre) {
                template.push(['core/heading', {
                    level: 3,
                    content: cleanContent(titre),
                    className: 'ressource-display__titre'
                }]);
            }

            // Auteur et année
            let metaContent = '';
            if (acfFields.acf?.auteur) {
                metaContent += '<strong>' + acfFields.acf.auteur + '</strong>';
            }
            if (acfFields.acf?.annee_parution) {
                if (metaContent) metaContent += ' • ';
                metaContent += acfFields.acf.annee_parution;
            }
            if (metaContent) {
                template.push(['core/paragraph', {
                    content: metaContent,
                    className: 'ressource-display__meta'
                }]);
            }

            // Extrait
            if (selectedRessource.excerpt?.rendered) {
                template.push(['core/paragraph', {
                    content: cleanContent(selectedRessource.excerpt.rendered),
                    className: 'ressource-display__excerpt'
                }]);
            }

            // Bouton de téléchargement
            if (acfFields.acf?.fichier) {
                const fichier = acfFields.acf.fichier;
                const fileUrl = typeof fichier === 'object' ? fichier.url : fichier;
                const fileTitle = typeof fichier === 'object' ? 
                    (fichier.title || fichier.filename || 'Télécharger le fichier') : 
                    'Télécharger le fichier';
                const fileSize = typeof fichier === 'object' && fichier.filesize ? 
                    ' (' + formatFileSize(fichier.filesize) + ')' : '';
                
                template.push(['core/buttons', {
                    className: 'ressource-display__download'
                }, [
                    ['core/button', {
                        text: fileTitle + fileSize,
                        url: fileUrl,
                        linkTarget: '_blank',
                        className: 'is-style-outline ressource-display__download-btn'
                    }]
                ]]);
            }

            // Critique
            if (acfFields.acf?.critique) {
                template.push(['core/paragraph', {
                    content: cleanContent(acfFields.acf.critique),
                    className: 'ressource-display__critique'
                }]);
            }

            // Autres champs ACF non gérés spécifiquement
            const displayedFields = ['titre', 'auteur', 'annee_parution', 'fichier', 'critique'];
            const otherFields = Object.keys(acfFields.acf || {}).filter(key => !displayedFields.includes(key));
            
            if (otherFields.length > 0) {
                template.push(['core/heading', {
                    level: 4,
                    content: 'Informations complémentaires',
                    className: 'ressource-display__additional-title'
                }]);
                
                otherFields.forEach(fieldName => {
                    const fieldValue = acfFields.acf[fieldName];
                    if (fieldValue) {
                        const fieldLabel = fieldName.charAt(0).toUpperCase() + fieldName.slice(1).replace(/_/g, ' ');
                        let fieldContent = '';
                        
                        // Gestion des différents types de champs
                        if (typeof fieldValue === 'object' && fieldValue.url) {
                            // Champ fichier/image
                            fieldContent = '<a href="' + fieldValue.url + '" target="_blank">' +
                                (fieldValue.title || fieldValue.filename || fieldValue.url) + '</a>';
                        } else if (Array.isArray(fieldValue)) {
                            // Tableau de valeurs
                            fieldContent = fieldValue.filter(v => v).join(', ');
                        } else if (typeof fieldValue === 'string' && fieldValue.startsWith('http')) {
                            // URL
                            fieldContent = '<a href="' + fieldValue + '" target="_blank">' + fieldValue + '</a>';
                        } else {
                            // Texte simple
                            fieldContent = fieldValue;
                        }
                        
                        template.push(['core/paragraph', {
                            content: '<strong>' + fieldLabel + ' :</strong> ' + fieldContent,
                            className: 'ressource-display__field ressource-display__field--' + fieldName
                        }]);
                    }
                });
            }

            return template;
        };

        // Fonction utilitaire pour formater la taille des fichiers
        const formatFileSize = (bytes) => {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        };


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
                            {!ressourceId && (
                                <div className="ressource-display__placeholder">
                                    <p>Veuillez sélectionner une ressource dans le panneau de droite pour injecter les données.</p>
                                </div>
                            )}
                            
                            <InnerBlocks
                                key={`innerblocks-${ressourceId || 'empty'}`}
                                template={createDynamicTemplate()}
                                templateLock={false}
                                allowedBlocks={[
                                    'core/heading',
                                    'core/paragraph', 
                                    'core/buttons',
                                    'core/button',
                                    'core/image',
                                    'core/separator',
                                    'core/spacer'
                                ]}
                            />
                        </div>
                    </div>
                </div>
            </>
        );
    },

    save: function() {
        // Le rendu est géré côté PHP via render_callback
        return <InnerBlocks.Content />;
    }
});
