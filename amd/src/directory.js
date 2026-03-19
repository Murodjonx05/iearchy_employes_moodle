// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Ieatchy modal interactions.
 *
 * @module      mod_iearchy/directory
 * @copyright   2026
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {
    var createPlaceholder = function(initials, className) {
        var placeholder = document.createElement('div');
        placeholder.className = className;
        placeholder.textContent = initials && initials.trim() ? initials.trim() : '?';
        return placeholder;
    };

    var createImage = function(imageUrl, alt, fallbackInitials, className, placeholderClass) {
        var image = document.createElement('img');
        image.className = className;
        image.src = imageUrl;
        image.alt = alt;
        image.loading = 'lazy';
        image.addEventListener('error', function() {
            var parent = image.parentNode;
            if (parent) {
                parent.innerHTML = '';
                parent.appendChild(createPlaceholder(fallbackInitials, placeholderClass));
            }
        });

        return image;
    };

    var initRoot = function(root) {
        var overlay = root.querySelector('#iearchOverlay');
        var closeButton = root.querySelector('#iearchClose');
        var modalImageWrap = root.querySelector('#iearchModalImgWrap');
        var modalName = root.querySelector('#iearchModalName');
        var modalPosition = root.querySelector('#iearchModalPosition');
        var modalDescription = root.querySelector('#iearchModalDesc');
        var lastFocused = null;
        var isInitialized = false;

        if (!overlay || !closeButton || !modalImageWrap || !modalName || !modalPosition || !modalDescription) {
            return;
        }

        if (root.dataset.iearchDirectoryInitialized === 'true') {
            return;
        }
        root.dataset.iearchDirectoryInitialized = 'true';

        var closeModal = function() {
            overlay.classList.remove('is-open');
            overlay.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';

            if (lastFocused) {
                lastFocused.focus();
                lastFocused = null;
            }
        };

        var openModal = function(card) {
            var fullname = card.dataset.fullname || '';
            var position = card.dataset.position || '';
            var imageUrl = card.dataset.imageUrl || '';
            var initials = card.dataset.initials || '?';
            var hasImage = card.dataset.hasImage === 'true' && imageUrl.trim() !== '';
            var descriptionNode = card.querySelector('.iearch_card__desc');
            var description = descriptionNode ? descriptionNode.textContent.trim() : '';

            modalImageWrap.innerHTML = '';
            if (hasImage) {
                modalImageWrap.appendChild(
                    createImage(imageUrl, fullname, initials, 'iearch_modal__img', 'iearch_modal__img-placeholder')
                );
            } else {
                modalImageWrap.appendChild(createPlaceholder(initials, 'iearch_modal__img-placeholder'));
            }

            modalName.textContent = fullname;
            modalPosition.textContent = position;
            modalDescription.textContent = description || '—';

            lastFocused = card;
            overlay.classList.add('is-open');
            overlay.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            closeButton.focus();
        };

        // Use event delegation because Moodle/YUI may re-render cards after init.
        root.addEventListener('click', function(event) {
            var target = event.target;
            if (!(target instanceof Element)) {
                return;
            }
            var card = target.closest('.iearch_card');
            if (!card || !root.contains(card)) {
                return;
            }
            openModal(card);
        });

        root.addEventListener('keydown', function(event) {
            if (event.key !== 'Enter' && event.key !== ' ') {
                return;
            }
            var target = event.target;
            if (!(target instanceof Element)) {
                return;
            }
            var card = target.closest('.iearch_card');
            if (!card || !root.contains(card)) {
                return;
            }
            event.preventDefault();
            openModal(card);
        });

        closeButton.addEventListener('click', closeModal);
        overlay.addEventListener('click', function(event) {
            if (event.target === overlay) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && overlay.classList.contains('is-open')) {
                closeModal();
            }
        });
    };

    return {
        init: function() {
            document.querySelectorAll('[data-region="mod-iearchy"]').forEach(initRoot);
        }
    };
});
