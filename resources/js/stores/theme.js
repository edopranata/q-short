import { defineStore } from 'pinia';
import { ref, watch } from 'vue';

export const useThemeStore = defineStore('theme', () => {
    const isDark = ref(false);
    const isInitialized = ref(false);

    const initTheme = () => {
        if (isInitialized.value) return;
        
        // Check for saved theme preference or default to system preference
        const savedTheme = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
            isDark.value = true;
        } else {
            isDark.value = false;
        }
        
        applyTheme();
        isInitialized.value = true;
        
        // Watch for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                isDark.value = e.matches;
                applyTheme();
            }
        });
    };
    
    const applyTheme = () => {
        const html = document.documentElement;
        
        if (isDark.value) {
            html.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            html.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    };
    
    const toggleTheme = () => {
        isDark.value = !isDark.value;
        applyTheme();
        
        // Announce theme change for screen readers
        const announcement = isDark.value ? 'Dark mode enabled' : 'Light mode enabled';
        announceToScreenReader(announcement);
    };
    
    const setTheme = (theme) => {
        isDark.value = theme === 'dark';
        applyTheme();
    };
    
    const announceToScreenReader = (message) => {
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.setAttribute('class', 'sr-only');
        announcement.textContent = message;
        
        document.body.appendChild(announcement);
        
        setTimeout(() => {
            document.body.removeChild(announcement);
        }, 1000);
    };
    
    // Watch for theme changes and apply them
    watch(isDark, () => {
        applyTheme();
    });
    
    return {
        isDark,
        isInitialized,
        initTheme,
        toggleTheme,
        setTheme
    };
});