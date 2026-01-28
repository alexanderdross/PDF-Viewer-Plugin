/**
 * Premium Viewer JavaScript
 *
 * @package PDF_Embed_SEO_Premium
 * @since   1.0.0
 */

(function($) {
    'use strict';

    var PremiumViewer = {
        pdfDoc: null,
        currentPage: 1,
        searchMatches: [],
        currentMatch: 0,
        bookmarksLoaded: false,

        init: function() {
            if (typeof pdfPremiumViewer === 'undefined') {
                return;
            }

            this.bindEvents();
            this.initSearch();
            this.initBookmarks();
            this.initProgressTracking();
            this.checkReadingProgress();
        },

        bindEvents: function() {
            var self = this;

            // Search toggle
            $(document).on('click', '.pdf-search-toggle', function(e) {
                e.preventDefault();
                self.toggleSearch();
            });

            // Search close
            $(document).on('click', '.pdf-search-close', function(e) {
                e.preventDefault();
                self.closeSearch();
            });

            // Search input
            $(document).on('input', '.pdf-search-input', $.debounce(300, function() {
                self.performSearch($(this).val());
            }));

            // Search navigation
            $(document).on('click', '.pdf-search-prev', function() {
                self.navigateSearch(-1);
            });
            $(document).on('click', '.pdf-search-next', function() {
                self.navigateSearch(1);
            });

            // Enter key in search
            $(document).on('keydown', '.pdf-search-input', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    self.navigateSearch(e.shiftKey ? -1 : 1);
                } else if (e.key === 'Escape') {
                    self.closeSearch();
                }
            });

            // Bookmarks toggle
            $(document).on('click', '.pdf-bookmarks-toggle', function(e) {
                e.preventDefault();
                self.toggleBookmarks();
            });

            // Bookmarks close
            $(document).on('click', '.pdf-bookmarks-close', function(e) {
                e.preventDefault();
                self.closeBookmarks();
            });

            // Bookmark click
            $(document).on('click', '.pdf-bookmarks-list a', function(e) {
                e.preventDefault();
                var dest = $(this).data('dest');
                self.navigateToBookmark(dest);
            });

            // Presentation mode
            $(document).on('click', '.pdf-presentation-toggle', function(e) {
                e.preventDefault();
                self.togglePresentation();
            });

            // Reading progress prompt
            $(document).on('click', '.pdf-progress-resume', function() {
                self.resumeReading();
            });
            $(document).on('click', '.pdf-progress-start-over', function() {
                self.startOver();
            });

            // Track page changes
            $(document).on('pdf:pagechange', function(e, pageNum) {
                self.currentPage = pageNum;
                self.saveReadingProgress();
            });

            // Keyboard shortcuts
            $(document).on('keydown', function(e) {
                if (e.ctrlKey || e.metaKey) {
                    if (e.key === 'f') {
                        e.preventDefault();
                        self.toggleSearch();
                    }
                }
            });
        },

        // Search functionality
        initSearch: function() {
            if (!pdfPremiumViewer.enableSearch) {
                $('.pdf-search-group').hide();
                return;
            }
        },

        toggleSearch: function() {
            var $panel = $('.pdf-search-panel');
            if ($panel.is(':visible')) {
                this.closeSearch();
            } else {
                $panel.show();
                $('.pdf-search-input').focus();
            }
        },

        closeSearch: function() {
            $('.pdf-search-panel').hide();
            this.clearSearchHighlights();
        },

        performSearch: function(query) {
            var self = this;

            if (!query || query.length < 2) {
                this.clearSearchHighlights();
                $('.pdf-search-results').text('');
                return;
            }

            this.searchMatches = [];
            this.currentMatch = 0;

            // PDF.js text search
            if (typeof pdfViewer !== 'undefined' && pdfViewer.pdfDoc) {
                var numPages = pdfViewer.pdfDoc.numPages;
                var searchPromises = [];

                for (var i = 1; i <= numPages; i++) {
                    searchPromises.push(this.searchPage(i, query));
                }

                Promise.all(searchPromises).then(function(results) {
                    results.forEach(function(pageMatches, index) {
                        pageMatches.forEach(function(match) {
                            self.searchMatches.push({
                                page: index + 1,
                                match: match
                            });
                        });
                    });

                    self.updateSearchResults();
                    if (self.searchMatches.length > 0) {
                        self.highlightCurrentMatch();
                    }
                });
            }
        },

        searchPage: function(pageNum, query) {
            return new Promise(function(resolve) {
                if (typeof pdfViewer !== 'undefined' && pdfViewer.pdfDoc) {
                    pdfViewer.pdfDoc.getPage(pageNum).then(function(page) {
                        page.getTextContent().then(function(textContent) {
                            var text = textContent.items.map(function(item) {
                                return item.str;
                            }).join(' ');

                            var matches = [];
                            var regex = new RegExp(query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'gi');
                            var match;

                            while ((match = regex.exec(text)) !== null) {
                                matches.push({
                                    index: match.index,
                                    text: match[0]
                                });
                            }

                            resolve(matches);
                        });
                    });
                } else {
                    resolve([]);
                }
            });
        },

        updateSearchResults: function() {
            var count = this.searchMatches.length;
            var text = count === 0
                ? pdfPremiumViewer.i18n.noResults
                : pdfPremiumViewer.i18n.matchesFound.replace('%d', count);
            $('.pdf-search-results').text(text);
        },

        navigateSearch: function(direction) {
            if (this.searchMatches.length === 0) {
                return;
            }

            this.currentMatch += direction;
            if (this.currentMatch >= this.searchMatches.length) {
                this.currentMatch = 0;
            } else if (this.currentMatch < 0) {
                this.currentMatch = this.searchMatches.length - 1;
            }

            this.highlightCurrentMatch();
        },

        highlightCurrentMatch: function() {
            var match = this.searchMatches[this.currentMatch];
            if (match && typeof pdfViewer !== 'undefined') {
                pdfViewer.goToPage(match.page);
                $(document).trigger('pdf:highlight', match);
            }
        },

        clearSearchHighlights: function() {
            $('.pdf-search-highlight').removeClass('pdf-search-highlight current');
        },

        // Bookmarks functionality
        initBookmarks: function() {
            if (!pdfPremiumViewer.enableBookmarks) {
                $('.pdf-bookmarks-group').hide();
                return;
            }

            // Add bookmarks sidebar to page
            if ($('.pdf-bookmarks-sidebar').length === 0) {
                $('body').append(
                    '<div class="pdf-bookmarks-sidebar" style="display:none;">' +
                        '<div class="pdf-bookmarks-header">' +
                            '<h4>' + pdfPremiumViewer.i18n.bookmarks + '</h4>' +
                            '<button type="button" class="pdf-bookmarks-close">&times;</button>' +
                        '</div>' +
                        '<div class="pdf-bookmarks-content">' +
                            '<ul class="pdf-bookmarks-list"></ul>' +
                            '<p class="pdf-no-bookmarks" style="display:none;">' + pdfPremiumViewer.i18n.noBookmarks + '</p>' +
                        '</div>' +
                    '</div>'
                );
            }
        },

        toggleBookmarks: function() {
            var $sidebar = $('.pdf-bookmarks-sidebar');

            if ($sidebar.is(':visible')) {
                this.closeBookmarks();
            } else {
                $sidebar.show();
                if (!this.bookmarksLoaded) {
                    this.loadBookmarks();
                }
            }
        },

        closeBookmarks: function() {
            $('.pdf-bookmarks-sidebar').hide();
        },

        loadBookmarks: function() {
            var self = this;

            if (typeof pdfViewer !== 'undefined' && pdfViewer.pdfDoc) {
                pdfViewer.pdfDoc.getOutline().then(function(outline) {
                    if (!outline || outline.length === 0) {
                        $('.pdf-bookmarks-list').hide();
                        $('.pdf-no-bookmarks').show();
                    } else {
                        var html = self.renderBookmarks(outline);
                        $('.pdf-bookmarks-list').html(html).show();
                        $('.pdf-no-bookmarks').hide();
                    }
                    self.bookmarksLoaded = true;
                });
            }
        },

        renderBookmarks: function(items, level) {
            var self = this;
            level = level || 0;
            var html = '';

            items.forEach(function(item) {
                html += '<li>';
                html += '<a href="#" data-dest="' + encodeURIComponent(JSON.stringify(item.dest)) + '" style="padding-left:' + (level * 15 + 10) + 'px;">';
                html += self.escapeHtml(item.title);
                html += '</a>';

                if (item.items && item.items.length > 0) {
                    html += '<ul>' + self.renderBookmarks(item.items, level + 1) + '</ul>';
                }

                html += '</li>';
            });

            return html;
        },

        navigateToBookmark: function(destJson) {
            var self = this;

            try {
                var dest = JSON.parse(decodeURIComponent(destJson));

                if (typeof pdfViewer !== 'undefined' && pdfViewer.pdfDoc) {
                    if (typeof dest === 'string') {
                        pdfViewer.pdfDoc.getDestination(dest).then(function(destArray) {
                            self.goToDestination(destArray);
                        });
                    } else if (Array.isArray(dest)) {
                        self.goToDestination(dest);
                    }
                }
            } catch (e) {
                console.error('Error navigating to bookmark:', e);
            }
        },

        goToDestination: function(destArray) {
            var self = this;

            if (!destArray || !destArray[0]) {
                return;
            }

            if (typeof pdfViewer !== 'undefined' && pdfViewer.pdfDoc) {
                pdfViewer.pdfDoc.getPageIndex(destArray[0]).then(function(pageIndex) {
                    pdfViewer.goToPage(pageIndex + 1);
                    self.closeBookmarks();
                });
            }
        },

        // Reading Progress
        initProgressTracking: function() {
            if (!pdfPremiumViewer.enableProgress) {
                return;
            }

            // Save progress periodically
            setInterval(this.saveReadingProgress.bind(this), 30000);
        },

        saveReadingProgress: function() {
            if (!pdfPremiumViewer.enableProgress || this.currentPage < 2) {
                return;
            }

            // For logged-in users, save via AJAX
            if (pdfPremiumViewer.userId > 0) {
                $.post(pdfPremiumViewer.ajaxUrl, {
                    action: 'pdf_save_reading_progress',
                    nonce: pdfPremiumViewer.nonce,
                    post_id: pdfPremiumViewer.postId,
                    page_number: this.currentPage
                });
            } else {
                // For guests, use localStorage
                localStorage.setItem('pdf_progress_' + pdfPremiumViewer.postId, this.currentPage);
            }
        },

        checkReadingProgress: function() {
            var self = this;

            if (!pdfPremiumViewer.enableProgress) {
                return;
            }

            var savedPage = 0;

            if (pdfPremiumViewer.userId > 0) {
                $.post(pdfPremiumViewer.ajaxUrl, {
                    action: 'pdf_get_reading_progress',
                    nonce: pdfPremiumViewer.nonce,
                    post_id: pdfPremiumViewer.postId
                }, function(response) {
                    if (response.success && response.data.page_number > 1) {
                        self.showProgressPrompt(response.data.page_number);
                    }
                });
            } else {
                savedPage = localStorage.getItem('pdf_progress_' + pdfPremiumViewer.postId);
                if (savedPage && parseInt(savedPage) > 1) {
                    this.showProgressPrompt(parseInt(savedPage));
                }
            }
        },

        showProgressPrompt: function(pageNum) {
            var message = pdfPremiumViewer.i18n.resumeReading.replace('%d', pageNum);

            $('body').append(
                '<div class="pdf-progress-prompt">' +
                    '<div class="pdf-progress-prompt-content">' +
                        '<p class="pdf-progress-message">' + message + '</p>' +
                        '<div class="pdf-progress-buttons">' +
                            '<button type="button" class="pdf-progress-resume button button-primary">' + pdfPremiumViewer.i18n.resume + '</button>' +
                            '<button type="button" class="pdf-progress-start-over button">' + pdfPremiumViewer.i18n.startOver + '</button>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );

            this.savedPage = pageNum;
        },

        resumeReading: function() {
            if (this.savedPage && typeof pdfViewer !== 'undefined') {
                pdfViewer.goToPage(this.savedPage);
            }
            $('.pdf-progress-prompt').remove();
        },

        startOver: function() {
            $('.pdf-progress-prompt').remove();
            // Clear saved progress
            if (pdfPremiumViewer.userId > 0) {
                $.post(pdfPremiumViewer.ajaxUrl, {
                    action: 'pdf_save_reading_progress',
                    nonce: pdfPremiumViewer.nonce,
                    post_id: pdfPremiumViewer.postId,
                    page_number: 1
                });
            } else {
                localStorage.removeItem('pdf_progress_' + pdfPremiumViewer.postId);
            }
        },

        // Presentation Mode
        togglePresentation: function() {
            if ($('.pdf-presentation-mode').length > 0) {
                this.exitPresentation();
            } else {
                this.enterPresentation();
            }
        },

        enterPresentation: function() {
            $('body').addClass('pdf-in-presentation');
            // Implementation would clone the PDF viewer into fullscreen mode
            // This is a simplified version
            if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen();
            }
        },

        exitPresentation: function() {
            $('body').removeClass('pdf-in-presentation');
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        },

        // Utility
        escapeHtml: function(text) {
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };

    // Debounce utility
    $.debounce = function(delay, callback) {
        var timer = null;
        return function() {
            var context = this;
            var args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function() {
                callback.apply(context, args);
            }, delay);
        };
    };

    // Initialize on document ready
    $(document).ready(function() {
        // Wait for PDF viewer to be ready
        $(document).on('pdf:ready', function() {
            PremiumViewer.init();
        });

        // Fallback if event doesn't fire
        setTimeout(function() {
            if (!PremiumViewer.pdfDoc) {
                PremiumViewer.init();
            }
        }, 2000);
    });

})(jQuery);
