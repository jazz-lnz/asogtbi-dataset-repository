<script>
(() => {
    const selectors = 'select:not([multiple]):not([data-native-select])';

    const closeSelect = (wrap) => {
        wrap.classList.remove('is-open');
        wrap.querySelector('.enhanced-select__button')?.setAttribute('aria-expanded', 'false');
    };

    const closeAll = (except = null) => {
        document.querySelectorAll('.enhanced-select.is-open').forEach((wrap) => {
            if (wrap !== except) {
                closeSelect(wrap);
            }
        });
    };

    const selectedLabel = (select) => {
        const option = select.options[select.selectedIndex];
        return option ? option.textContent.trim() : 'Select option';
    };

    const enhanceSelect = (select, index) => {
        if (select.dataset.enhancedSelect === 'true') {
            return;
        }

        select.dataset.enhancedSelect = 'true';
        select.classList.add('enhanced-select-native');
        select.tabIndex = -1;
        select.setAttribute('aria-hidden', 'true');

        const wrap = document.createElement('div');
        wrap.className = 'enhanced-select';

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'enhanced-select__button';
        button.setAttribute('aria-haspopup', 'listbox');
        button.setAttribute('aria-expanded', 'false');

        const menuId = `enhanced-select-menu-${Date.now()}-${index}`;
        button.setAttribute('aria-controls', menuId);

        const value = document.createElement('span');
        value.className = 'enhanced-select__value';

        const chevron = document.createElement('span');
        chevron.className = 'enhanced-select__chevron';
        chevron.setAttribute('aria-hidden', 'true');

        const menu = document.createElement('div');
        menu.className = 'enhanced-select__menu';
        menu.id = menuId;
        menu.setAttribute('role', 'listbox');

        button.append(value, chevron);
        wrap.append(button, menu);
        select.insertAdjacentElement('afterend', wrap);

        const sync = () => {
            value.textContent = selectedLabel(select);
            button.disabled = select.disabled;
            wrap.classList.toggle('is-disabled', select.disabled);

            menu.querySelectorAll('.enhanced-select__option').forEach((option) => {
                const isSelected = Number(option.dataset.index) === select.selectedIndex;
                option.classList.toggle('is-selected', isSelected);
                option.setAttribute('aria-selected', isSelected ? 'true' : 'false');
            });
        };

        const renderOptions = () => {
            menu.innerHTML = '';

            Array.from(select.options).forEach((option, optionIndex) => {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'enhanced-select__option';
                item.dataset.index = String(optionIndex);
                item.textContent = option.textContent;
                item.setAttribute('role', 'option');
                item.setAttribute('aria-selected', option.selected ? 'true' : 'false');

                if (option.disabled) {
                    item.classList.add('is-disabled');
                    item.disabled = true;
                }

                if (option.selected) {
                    item.classList.add('is-selected');
                }

                item.addEventListener('click', () => {
                    if (option.disabled) {
                        return;
                    }

                    select.selectedIndex = optionIndex;
                    select.dispatchEvent(new Event('input', { bubbles: true }));
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    sync();
                    closeSelect(wrap);
                    button.focus();
                });

                menu.appendChild(item);
            });
        };

        const focusOption = (direction) => {
            const options = Array.from(menu.querySelectorAll('.enhanced-select__option:not(.is-disabled)'));
            if (!options.length) {
                return;
            }

            const currentIndex = options.indexOf(document.activeElement);
            let nextIndex = currentIndex;

            if (direction === 'first') {
                nextIndex = 0;
            } else if (direction === 'last') {
                nextIndex = options.length - 1;
            } else {
                nextIndex = currentIndex + direction;
            }

            if (nextIndex < 0) {
                nextIndex = options.length - 1;
            }

            if (nextIndex >= options.length) {
                nextIndex = 0;
            }

            options[nextIndex].focus();
        };

        const open = () => {
            if (select.disabled) {
                return;
            }

            renderOptions();
            closeAll(wrap);
            wrap.classList.add('is-open');
            button.setAttribute('aria-expanded', 'true');

            window.requestAnimationFrame(() => {
                const target = menu.querySelector('.enhanced-select__option.is-selected:not(.is-disabled)')
                    || menu.querySelector('.enhanced-select__option:not(.is-disabled)');
                target?.focus();
                target?.scrollIntoView({ block: 'nearest' });
            });
        };

        button.addEventListener('click', () => {
            if (wrap.classList.contains('is-open')) {
                closeSelect(wrap);
            } else {
                open();
            }
        });

        button.addEventListener('keydown', (event) => {
            if (['ArrowDown', 'ArrowUp', 'Enter', ' '].includes(event.key)) {
                event.preventDefault();
                open();
            }

            if (event.key === 'Escape') {
                closeSelect(wrap);
            }
        });

        menu.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                event.preventDefault();
                closeSelect(wrap);
                button.focus();
            }

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                focusOption(1);
            }

            if (event.key === 'ArrowUp') {
                event.preventDefault();
                focusOption(-1);
            }

            if (event.key === 'Home') {
                event.preventDefault();
                focusOption('first');
            }

            if (event.key === 'End') {
                event.preventDefault();
                focusOption('last');
            }
        });

        wrap.addEventListener('focusout', (event) => {
            if (!wrap.contains(event.relatedTarget)) {
                closeSelect(wrap);
            }
        });

        select.addEventListener('change', sync);
        sync();
    };

    document.querySelectorAll(selectors).forEach(enhanceSelect);

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.enhanced-select')) {
            closeAll();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeAll();
        }
    });
})();
</script>
