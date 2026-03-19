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
 * Pointer-based reordering for iearchy management pages.
 *
 * @module      mod_iearchy/reorder
 * @copyright   2026
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {
    var activeState = null;

    var captureOrder = function(list) {
        return Array.from(list.querySelectorAll('[data-region="sortable-item"]')).map(function(item) {
            return item.dataset.id;
        });
    };

    var syncOrderLabels = function(list) {
        list.querySelectorAll('[data-region="sortable-item"]').forEach(function(item, index) {
            var label = item.querySelector('[data-region="order-label"]');
            if (label) {
                label.textContent = String(index + 1);
            }
        });
    };

    var restoreOrder = function(list, ids) {
        var items = {};
        list.querySelectorAll('[data-region="sortable-item"]').forEach(function(item) {
            items[item.dataset.id] = item;
        });

        ids.forEach(function(id) {
            if (items[id]) {
                list.appendChild(items[id]);
            }
        });

        syncOrderLabels(list);
    };

    var saveOrder = function(list, previousOrder) {
        var ids = captureOrder(list);
        var params = new URLSearchParams();

        params.append('sesskey', M.cfg.sesskey);
        params.append('id', list.dataset.cmid);
        params.append('action', list.dataset.action);
        params.append('orderedids', ids.join(','));

        if (list.dataset.levelid) {
            params.append('levelid', list.dataset.levelid);
        }

        fetch(list.dataset.url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: params.toString()
        }).then(function(response) {
            if (!response.ok) {
                throw new Error('Unable to save sort order');
            }

            syncOrderLabels(list);
        }).catch(function(error) {
            restoreOrder(list, previousOrder);
            window.console.error(error);
        });
    };

    var reorderFromPointer = function(state, clientY) {
        var list = state.list;
        var dragged = state.item;
        var items = Array.from(list.querySelectorAll('[data-region="sortable-item"]')).filter(function(item) {
            return item !== dragged;
        });

        var beforeItem = null;
        items.some(function(item) {
            var rect = item.getBoundingClientRect();
            if (clientY < rect.top + rect.height / 2) {
                beforeItem = item;
                return true;
            }

            return false;
        });

        if (beforeItem) {
            if (beforeItem.previousSibling !== dragged) {
                list.insertBefore(dragged, beforeItem);
            }
        } else if (list.lastElementChild !== dragged) {
            list.appendChild(dragged);
        }
    };

    var stopDragging = function(cancelled) {
        if (!activeState) {
            return;
        }

        var state = activeState;
        var newOrder = captureOrder(state.list);

        state.item.classList.remove('is-dragging');
        document.body.classList.remove('iearchy-is-sorting');
        document.removeEventListener('pointermove', state.onMove);
        document.removeEventListener('pointerup', state.onUp);
        document.removeEventListener('pointercancel', state.onCancel);

        activeState = null;

        if (cancelled) {
            restoreOrder(state.list, state.previousOrder);
            return;
        }

        if (newOrder.join(',') !== state.previousOrder.join(',')) {
            saveOrder(state.list, state.previousOrder);
        } else {
            syncOrderLabels(state.list);
        }
    };

    var startDragging = function(list, item, event) {
        if (activeState) {
            stopDragging(true);
        }

        var state = {
            list: list,
            item: item,
            previousOrder: captureOrder(list),
            onMove: null,
            onUp: null,
            onCancel: null
        };

        state.onMove = function(moveEvent) {
            moveEvent.preventDefault();
            reorderFromPointer(state, moveEvent.clientY);
        };

        state.onUp = function() {
            stopDragging(false);
        };

        state.onCancel = function() {
            stopDragging(true);
        };

        activeState = state;
        item.classList.add('is-dragging');
        document.body.classList.add('iearchy-is-sorting');
        document.addEventListener('pointermove', state.onMove);
        document.addEventListener('pointerup', state.onUp);
        document.addEventListener('pointercancel', state.onCancel);

        event.preventDefault();
    };

    var attachList = function(list) {
        list.querySelectorAll('[data-region="sortable-item"]').forEach(function(item) {
            var handle = item.querySelector('.iearchy-admin-item__handle') || item;

            handle.addEventListener('pointerdown', function(event) {
                if (event.button !== 0) {
                    return;
                }

                startDragging(list, item, event);
            });
        });

        syncOrderLabels(list);
    };

    return {
        init: function() {
            document.querySelectorAll('[data-region="sortable-list"]').forEach(attachList);
        }
    };
});
