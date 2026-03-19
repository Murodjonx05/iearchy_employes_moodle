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
        var overlay = root.querySelector('#sdOverlay');
        var closeButton = root.querySelector('#sdClose');
        var modalImageWrap = root.querySelector('#sdModalImgWrap');
        var modalName = root.querySelector('#sdModalName');
        var modalPosition = root.querySelector('#sdModalPosition');
        var modalDescription = root.querySelector('#sdModalDesc');
        var lastFocused = null;

        if (!overlay || !closeButton || !modalImageWrap || !modalName || !modalPosition || !modalDescription) {
            return;
        }

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
            var descriptionNode = card.querySelector('.sd-card__desc');
            var description = descriptionNode ? descriptionNode.textContent.trim() : '';

            modalImageWrap.innerHTML = '';
            if (hasImage) {
                modalImageWrap.appendChild(
                    createImage(imageUrl, fullname, initials, 'sd-modal__img', 'sd-modal__img-placeholder')
                );
            } else {
                modalImageWrap.appendChild(createPlaceholder(initials, 'sd-modal__img-placeholder'));
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

        root.querySelectorAll('.sd-card').forEach(function(card) {
            card.addEventListener('click', function() {
                openModal(card);
            });

            card.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    openModal(card);
                }
            });
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
