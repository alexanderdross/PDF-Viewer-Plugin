/**
 * PDF Embed & SEO Pro+ Annotations Scripts
 *
 * @package PDF_Embed_SEO_Optimize
 * @subpackage Pro_Plus
 * @since 1.3.0
 */

(function($) {
    'use strict';

    /**
     * Annotations Controller
     */
    var PdfAnnotations = {
        config: {},
        annotations: [],
        currentPage: 1,
        editMode: false,
        selectedType: 'note',

        /**
         * Initialize annotations
         */
        init: function() {
            if (typeof pdfAnnotations === 'undefined') {
                return;
            }

            this.config = pdfAnnotations;

            if (!this.config.annotationsEnabled && !this.config.signaturesEnabled) {
                return;
            }

            this.createAnnotationsLayer();
            this.loadAnnotations();
            this.bindEvents();
        },

        /**
         * Create annotations layer
         */
        createAnnotationsLayer: function() {
            var $container = $('.pdf-viewer-container');

            if (!$container.length) {
                return;
            }

            $container.css('position', 'relative');
            $container.append('<div class="pdf-annotations-layer"></div>');
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            var self = this;

            // Page change
            $(document).on('pdf:pagechange', function(e, data) {
                self.currentPage = data.page || 1;
                self.renderAnnotations();
            });

            // Click on annotations layer
            $('.pdf-annotations-layer').on('click', function(e) {
                if (!self.editMode || !self.config.canAnnotate) {
                    return;
                }

                // Get relative position
                var offset = $(this).offset();
                var x = ((e.pageX - offset.left) / $(this).width()) * 100;
                var y = ((e.pageY - offset.top) / $(this).height()) * 100;

                self.createAnnotation(x, y);
            });

            // Click on annotation
            $(document).on('click', '.pdf-annotation', function(e) {
                e.stopPropagation();
                var annotationId = $(this).data('id');
                self.openAnnotationPopup(annotationId);
            });

            // Close popup
            $(document).on('click', '.pdf-annotation-popup .cancel', function() {
                self.closeAnnotationPopup();
            });

            // Save annotation
            $(document).on('click', '.pdf-annotation-popup .save', function() {
                self.saveAnnotationContent();
            });

            // Delete annotation
            $(document).on('click', '.pdf-annotation-popup .delete', function() {
                self.deleteAnnotation();
            });

            // Toggle edit mode
            $(document).on('pdf:annotate:toggle', function(e, enabled) {
                self.editMode = enabled;
                $('.pdf-annotations-layer').toggleClass('editing', enabled);
            });
        },

        /**
         * Load annotations from server
         */
        loadAnnotations: function() {
            var self = this;

            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'pdf_get_annotations',
                    nonce: this.config.nonce,
                    post_id: this.config.postId
                },
                success: function(response) {
                    if (response.success) {
                        self.annotations = response.data;
                        self.renderAnnotations();
                    }
                }
            });
        },

        /**
         * Render annotations for current page
         */
        renderAnnotations: function() {
            var self = this;
            var $layer = $('.pdf-annotations-layer');

            $layer.empty();

            var pageAnnotations = this.annotations.filter(function(a) {
                return parseInt(a.page_number) === self.currentPage;
            });

            pageAnnotations.forEach(function(annotation) {
                self.renderAnnotation(annotation);
            });
        },

        /**
         * Render single annotation
         */
        renderAnnotation: function(annotation) {
            var $layer = $('.pdf-annotations-layer');

            var $el = $('<div class="pdf-annotation"></div>')
                .addClass(annotation.annotation_type)
                .data('id', annotation.id)
                .css({
                    left: annotation.x_position + '%',
                    top: annotation.y_position + '%',
                    backgroundColor: annotation.color,
                    opacity: annotation.opacity
                });

            if (annotation.width) {
                $el.css('width', annotation.width + '%');
            }

            if (annotation.height) {
                $el.css('height', annotation.height + '%');
            }

            $layer.append($el);
        },

        /**
         * Create new annotation
         */
        createAnnotation: function(x, y) {
            var self = this;

            var data = {
                action: 'pdf_save_annotation',
                nonce: this.config.nonce,
                post_id: this.config.postId,
                page_number: this.currentPage,
                annotation_type: this.selectedType,
                x_position: x,
                y_position: y,
                color: '#ffff00',
                opacity: 1,
                content: ''
            };

            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        var annotation = $.extend({}, data, {
                            id: response.data.id,
                            is_owner: true,
                            user_name: ''
                        });

                        self.annotations.push(annotation);
                        self.renderAnnotation(annotation);
                        self.openAnnotationPopup(response.data.id);
                    }
                }
            });
        },

        /**
         * Open annotation popup
         */
        openAnnotationPopup: function(annotationId) {
            this.closeAnnotationPopup();

            var annotation = this.annotations.find(function(a) {
                return parseInt(a.id) === parseInt(annotationId);
            });

            if (!annotation) {
                return;
            }

            var $annotation = $('.pdf-annotation[data-id="' + annotationId + '"]');
            var position = $annotation.position();

            var html = '<div class="pdf-annotation-popup" data-id="' + annotationId + '">' +
                '<div class="pdf-annotation-popup-header">' +
                '<span class="author">' + (annotation.user_name || 'You') + '</span>' +
                '<span class="date">' + (annotation.created_at || 'Just now') + '</span>' +
                '</div>' +
                '<div class="pdf-annotation-popup-content">' +
                '<textarea placeholder="' + this.config.i18n.addNote + '">' + (annotation.content || '') + '</textarea>' +
                '</div>' +
                '<div class="pdf-annotation-popup-actions">';

            if (annotation.is_owner) {
                html += '<button type="button" class="delete">' + this.config.i18n.delete + '</button>';
            }

            html += '<button type="button" class="cancel">' + this.config.i18n.cancel + '</button>' +
                '<button type="button" class="save">' + this.config.i18n.save + '</button>' +
                '</div>' +
                '</div>';

            var $popup = $(html);

            $popup.css({
                left: Math.min(position.left + 30, $(window).width() - 320),
                top: position.top
            });

            $('.pdf-annotations-layer').append($popup);
            $popup.find('textarea').focus();
        },

        /**
         * Close annotation popup
         */
        closeAnnotationPopup: function() {
            $('.pdf-annotation-popup').remove();
        },

        /**
         * Save annotation content
         */
        saveAnnotationContent: function() {
            var self = this;
            var $popup = $('.pdf-annotation-popup');
            var annotationId = $popup.data('id');
            var content = $popup.find('textarea').val();

            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'pdf_save_annotation',
                    nonce: this.config.nonce,
                    annotation_id: annotationId,
                    content: content
                },
                success: function(response) {
                    if (response.success) {
                        // Update local annotation
                        var annotation = self.annotations.find(function(a) {
                            return parseInt(a.id) === parseInt(annotationId);
                        });

                        if (annotation) {
                            annotation.content = content;
                        }

                        self.closeAnnotationPopup();
                    }
                }
            });
        },

        /**
         * Delete annotation
         */
        deleteAnnotation: function() {
            var self = this;
            var $popup = $('.pdf-annotation-popup');
            var annotationId = $popup.data('id');

            if (!confirm('Delete this annotation?')) {
                return;
            }

            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'pdf_delete_annotation',
                    nonce: this.config.nonce,
                    annotation_id: annotationId
                },
                success: function(response) {
                    if (response.success) {
                        // Remove from local array
                        self.annotations = self.annotations.filter(function(a) {
                            return parseInt(a.id) !== parseInt(annotationId);
                        });

                        // Remove from DOM
                        $('.pdf-annotation[data-id="' + annotationId + '"]').remove();
                        self.closeAnnotationPopup();
                    }
                }
            });
        },

        /**
         * Set annotation type
         */
        setType: function(type) {
            this.selectedType = type;
        }
    };

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        PdfAnnotations.init();
    });

})(jQuery);
