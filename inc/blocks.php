<?php

register_block_style( 'core/button', [
		'name' => 'white',
		'label' => 'White'
	] );

	register_block_style( 'core/media-text', [
		'name' => 'hero',
		'label' => 'Hero'
	] );

	register_block_style( 'core/media-text', [
		'name' => 'dark-bg',
		'label' => 'Dark BG'
	] );

	register_block_style( 'core/media-text', [
		'name' => 'light-bg',
		'label' => 'Light BG'
	] );

	register_block_style( 'core/media-text', [
		'name' => 'orange-bg',
		'label' => 'Orange BG'
	] );

	register_block_style( 'core/media-text', [
		'name' => 'full-orange-bg',
		'label' => 'Full Orange BG'
	] );

	register_block_style( 'acf/app-cards-row-cards', [
		'name' => 'simple',
		'label' => 'Simple'
	] );

	// Styles pour Peps Banner Image Section
	register_block_style( 'acf/app-peps-banner-image', [
		'name' => 'orange',
		'label' => 'Orange'
	] );

	register_block_style( 'acf/app-peps-banner-image', [
		'name' => 'bleu',
		'label' => 'Bleu'
	] );

	// Style de bloc "Bords arrondis" pour les colonnes
	register_block_style('core/column', [
		'name' => 'bords-arrondis',
		'label' => 'Bords arrondis'
	]);

	add_filter( 'render_block', function( $block_content, $block ) {
		if ( $block['blockName'] === 'core/media-text' && $block['attrs']['mediaType'] === 'video' ) {
			$block_content = str_replace( '<video controls', '<video playsinline autoplay loop muted', $block_content );
		}

		return $block_content;
	}, 10, 2 );

	add_filter( 'render_block', function( $block_content, $block ) {
		if ( $block['blockName'] === 'core/media-text' ) {
			$block_content = '<section class="wp-block-media-text-wrapper">' . $block_content . '</section>';
		}

		return $block_content;
	}, 10, 2 );

	add_filter( 'acf/register_block_type_args', function( $args ) {
		if ( ! str_starts_with( $args['name'], 'acf/app-' ) ) {
			return $args;
		}

		$args['supports']['color']['__experimentalDefaultControls'] = [
			'text' => true,
			'background' => true
		];

		$args['supports']['spacing'] = [
			'margin' => true
		];

		return $args;
	}, 10, 1);

	// Ajout d'une classe "large-image" pour l'élément image dans la section CardsRowSection
	add_filter( 'render_block', function( $block_content, $block ) {
		// Vérifie si le bloc est une carte de la section cards-row
		if (
			isset( $block['blockName'] ) &&
			$block['blockName'] === 'acf/app-cards-row-cards-card'
		) {
			// Ajoute la classe "large-image" à l'image si elle existe dans le contenu du bloc
			$block_content = preg_replace(
				'/<img([^>]*)class="([^"]*)"/i',
				'<img$1class="$2 large-image"',
				$block_content
			);

			// Si aucune classe n'existe, on ajoute l'attribut class avec "large-image"
			$block_content = preg_replace(
				'/<img((?:(?!class=)[^>])*)>/i',
				'<img$1 class="large-image">',
				$block_content
			);
		}
	return $block_content;
}, 15, 2 );
