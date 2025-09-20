/**
 * Configuration des supports de thème pour les blocs
 */

// Activer les fonctionnalités de typographie pour tous les blocs
wp.domReady(() => {
    // Activer les contrôles de typographie pour les blocs core
    wp.blocks.registerBlockStyle('core/heading', {
        name: 'ressource-title',
        label: 'Titre de ressource',
        isDefault: false
    });

    // Étendre les supports des blocs existants
    const { addFilter } = wp.hooks;

    // Ajouter les supports de typographie aux blocs heading
    addFilter(
        'blocks.registerBlockType',
        'infokiosque/extend-heading-supports',
        (settings, name) => {
            if (name === 'core/heading') {
                return {
                    ...settings,
                    supports: {
                        ...settings.supports,
                        typography: {
                            fontSize: true,
                            fontFamily: true,
                            fontStyle: true,
                            fontWeight: true,
                            letterSpacing: true,
                            lineHeight: true,
                            textDecoration: true,
                            textTransform: true
                        },
                        color: {
                            background: true,
                            text: true,
                            link: true
                        },
                        spacing: {
                            margin: true,
                            padding: true
                        }
                    }
                };
            }
            return settings;
        }
    );

    // Ajouter les supports de typographie aux blocs paragraph
    addFilter(
        'blocks.registerBlockType',
        'infokiosque/extend-paragraph-supports',
        (settings, name) => {
            if (name === 'core/paragraph') {
                return {
                    ...settings,
                    supports: {
                        ...settings.supports,
                        typography: {
                            fontSize: true,
                            fontFamily: true,
                            fontStyle: true,
                            fontWeight: true,
                            letterSpacing: true,
                            lineHeight: true,
                            textDecoration: true,
                            textTransform: true
                        }
                    }
                };
            }
            return settings;
        }
    );
});
