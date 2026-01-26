/**
 * PDF Viewer Gutenberg Block
 *
 * @package PDF_Embed_SEO
 */

( function( wp ) {
	'use strict';

	const { registerBlockType } = wp.blocks;
	const { createElement, Fragment, useState, useEffect } = wp.element;
	const { InspectorControls, useBlockProps } = wp.blockEditor;
	const { PanelBody, SelectControl, TextControl, Placeholder, Spinner } = wp.components;
	const { __ } = wp.i18n;
	const el = createElement;

	// Get localized data.
	const blockData = window.pdfEmbedSeoBlock || {};
	const pdfs = blockData.pdfs || [];
	const i18n = blockData.i18n || {};

	// PDF icon SVG.
	const pdfIcon = el(
		'svg',
		{
			width: 24,
			height: 24,
			viewBox: '0 0 24 24',
			fill: 'none',
			xmlns: 'http://www.w3.org/2000/svg'
		},
		el( 'path', {
			d: 'M14 2H6C4.9 2 4 2.9 4 4V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V8L14 2Z',
			fill: '#e53935'
		}),
		el( 'path', {
			d: 'M14 2V8H20',
			fill: '#ffcdd2'
		}),
		el( 'text', {
			x: '12',
			y: '16',
			textAnchor: 'middle',
			fontSize: '6',
			fill: '#ffffff',
			fontWeight: 'bold'
		}, 'PDF' )
	);

	/**
	 * Build PDF options for select control.
	 */
	function getPdfOptions() {
		const options = [
			{ value: 0, label: '— ' + ( i18n.selectPdf || 'Select a PDF Document' ) + ' —' }
		];

		pdfs.forEach( function( pdf ) {
			options.push( {
				value: pdf.value,
				label: pdf.label
			});
		});

		return options;
	}

	/**
	 * Get selected PDF data.
	 */
	function getSelectedPdf( pdfId ) {
		return pdfs.find( function( pdf ) {
			return pdf.value === pdfId;
		});
	}

	/**
	 * Edit component for the block.
	 */
	function EditBlock( props ) {
		const { attributes, setAttributes } = props;
		const { pdfId, width, height } = attributes;
		const blockProps = useBlockProps();

		const selectedPdf = getSelectedPdf( pdfId );

		// No PDFs available.
		if ( pdfs.length === 0 ) {
			return el(
				'div',
				blockProps,
				el( Placeholder, {
					icon: pdfIcon,
					label: i18n.blockTitle || 'PDF Viewer',
					instructions: i18n.noPdfsAvailable || 'No PDF documents available. Create one first.',
				})
			);
		}

		// No PDF selected - show selection UI.
		if ( ! pdfId || pdfId === 0 ) {
			return el(
				'div',
				blockProps,
				el(
					Fragment,
					null,
					// Inspector controls.
					el(
						InspectorControls,
						null,
						el(
							PanelBody,
							{ title: i18n.dimensions || 'Dimensions', initialOpen: true },
							el( TextControl, {
								label: i18n.width || 'Width',
								value: width,
								onChange: function( value ) {
									setAttributes( { width: value } );
								},
								help: 'e.g., 100%, 800px, 50vw'
							}),
							el( TextControl, {
								label: i18n.height || 'Height',
								value: height,
								onChange: function( value ) {
									setAttributes( { height: value } );
								},
								help: 'e.g., 600px, 80vh'
							})
						)
					),
					// Placeholder with PDF selection.
					el( Placeholder, {
						icon: pdfIcon,
						label: i18n.blockTitle || 'PDF Viewer',
						instructions: i18n.noPdfSelected || 'No PDF selected. Choose a PDF document from the dropdown.',
					},
						el( SelectControl, {
							value: pdfId,
							options: getPdfOptions(),
							onChange: function( value ) {
								setAttributes( { pdfId: parseInt( value, 10 ) } );
							},
							className: 'pdf-embed-seo-block-select'
						})
					)
				)
			);
		}

		// PDF selected - show preview.
		return el(
			'div',
			blockProps,
			el(
				Fragment,
				null,
				// Inspector controls.
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: i18n.selectPdf || 'Select PDF', initialOpen: true },
						el( SelectControl, {
							label: 'PDF Document',
							value: pdfId,
							options: getPdfOptions(),
							onChange: function( value ) {
								setAttributes( { pdfId: parseInt( value, 10 ) } );
							}
						})
					),
					el(
						PanelBody,
						{ title: i18n.dimensions || 'Dimensions', initialOpen: false },
						el( TextControl, {
							label: i18n.width || 'Width',
							value: width,
							onChange: function( value ) {
								setAttributes( { width: value } );
							},
							help: 'e.g., 100%, 800px, 50vw'
						}),
						el( TextControl, {
							label: i18n.height || 'Height',
							value: height,
							onChange: function( value ) {
								setAttributes( { height: value } );
							},
							help: 'e.g., 600px, 80vh'
						})
					)
				),
				// Preview.
				el(
					'div',
					{
						className: 'pdf-embed-seo-block-preview',
						style: {
							width: width,
							minHeight: '200px',
							maxHeight: height
						}
					},
					el(
						'div',
						{ className: 'pdf-embed-seo-block-preview-inner' },
						// Thumbnail if available.
						selectedPdf && selectedPdf.thumbnail ?
							el( 'img', {
								src: selectedPdf.thumbnail,
								alt: selectedPdf.label,
								className: 'pdf-embed-seo-block-thumbnail'
							})
						: el( 'div', { className: 'pdf-embed-seo-block-icon' }, pdfIcon ),
						// Title and info.
						el(
							'div',
							{ className: 'pdf-embed-seo-block-info' },
							el( 'strong', null, selectedPdf ? selectedPdf.label : 'PDF Document' ),
							el( 'span', { className: 'pdf-embed-seo-block-dimensions' }, width + ' × ' + height ),
							selectedPdf && selectedPdf.permalink ?
								el( 'a', {
									href: selectedPdf.permalink,
									target: '_blank',
									rel: 'noopener noreferrer',
									className: 'pdf-embed-seo-block-link'
								}, i18n.viewPdf || 'View PDF' )
							: null
						)
					)
				)
			)
		);
	}

	/**
	 * Register the block.
	 */
	registerBlockType( 'pdf-embed-seo/pdf-viewer', {
		title: i18n.blockTitle || 'PDF Viewer',
		description: i18n.blockDescription || 'Embed a PDF document from your library.',
		icon: pdfIcon,
		category: 'embed',
		keywords: [ 'pdf', 'document', 'embed', 'viewer' ],
		supports: {
			align: [ 'wide', 'full' ],
			html: false
		},
		attributes: {
			pdfId: {
				type: 'number',
				default: 0
			},
			width: {
				type: 'string',
				default: '100%'
			},
			height: {
				type: 'string',
				default: '600px'
			},
			align: {
				type: 'string',
				default: 'none'
			}
		},
		edit: EditBlock,
		save: function() {
			// Dynamic block - rendered on server.
			return null;
		}
	});

} )( window.wp );
