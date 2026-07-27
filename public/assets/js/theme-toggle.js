/**
 * Theme Toggle - Dark/Light Mode Switching
 * Follows system preference on first visit, persists user choice across sessions
 */

(function() {
  'use strict';

  const STORAGE_KEY = 'asog-theme';
  const OLD_STORAGE_KEY = 'theme-preference';
  const DARK_CLASS = 'dark';

  /**
   * Migrate legacy theme key to the current one
   */
  function migrateStorageKey() {
    const current = localStorage.getItem(STORAGE_KEY);
    if (current === 'dark' || current === 'light') {
      return; // Already using the correct key
    }
    const old = localStorage.getItem(OLD_STORAGE_KEY);
    if (old === 'dark' || old === 'light') {
      localStorage.setItem(STORAGE_KEY, old);
      localStorage.removeItem(OLD_STORAGE_KEY);
    }
  }

  /**
   * Get the user's theme preference
   * @returns {'light' | 'dark'}
   */
  function getThemePreference() {
    migrateStorageKey();
    const stored = localStorage.getItem(STORAGE_KEY);
    if (stored === 'dark' || stored === 'light') {
      return stored;
    }
    // Follow system preference
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  }

  /**
   * Apply theme to document
   * @param {'light' | 'dark'} theme
   */
  function applyTheme(theme) {
    if (theme === 'dark') {
      document.documentElement.classList.add(DARK_CLASS);
    } else {
      document.documentElement.classList.remove(DARK_CLASS);
    }
    // Update data-theme attribute for CSS selectors
    document.documentElement.setAttribute('data-theme', theme);
    // Update meta theme-color for mobile browsers
    const metaThemeColor = document.querySelector('meta[name="theme-color"]');
    if (metaThemeColor) {
      metaThemeColor.content = theme === 'dark' ? '#020d18' : '#03558b';
    }
  }

  /**
   * Toggle between light and dark themes
   */
  function toggleTheme() {
    const current = document.documentElement.classList.contains(DARK_CLASS) ? 'dark' : 'light';
    const next = current === 'dark' ? 'light' : 'dark';
    
    applyTheme(next);
    localStorage.setItem(STORAGE_KEY, next);
    
    // Dispatch custom event for other scripts
    window.dispatchEvent(new CustomEvent('themechange', { detail: { theme: next } }));
    
    // Re-sync header styles after theme change
    if (typeof window.syncHeader === 'function') window.syncHeader();
  }

  /**
   * Initialize theme on page load
   */
  function initTheme() {
    const theme = getThemePreference();
    applyTheme(theme);
  }

  // Initialize immediately
  initTheme();

  // Set up toggle button when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupToggle);
  } else {
    setupToggle();
  }

  function setupToggle() {
    const toggleBtn = document.querySelector('.theme-toggle');
    if (toggleBtn) {
      toggleBtn.addEventListener('click', toggleTheme);
      // Update icon based on current theme
      updateToggleIcon();
      window.addEventListener('themechange', updateToggleIcon);
    }
  }

  function updateToggleIcon() {
    const toggleBtn = document.querySelector('.theme-toggle');
    if (!toggleBtn) return;
    
    const isDark = document.documentElement.classList.contains(DARK_CLASS);
    const icon = toggleBtn.querySelector('.material-symbols-rounded');
    if (icon) {
      icon.textContent = isDark ? 'light_mode' : 'dark_mode';
    }
    toggleBtn.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
    toggleBtn.setAttribute('title', isDark ? 'Switch to light mode' : 'Switch to dark mode');
  }

  // Listen for system theme changes
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    if (!localStorage.getItem(STORAGE_KEY)) {
      applyTheme(e.matches ? 'dark' : 'light');
    }
  });

  // Expose for external use
  window.ThemeToggle = {
    toggle: toggleTheme,
    getTheme: getThemePreference,
    applyTheme: applyTheme
  };
})();
