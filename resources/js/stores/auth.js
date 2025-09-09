import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null);
    const loading = ref(false);
    const error = ref(null);

    // Computed properties
    const isAuthenticated = computed(() => !!user.value);
    const userName = computed(() => user.value?.name || '');
    const userEmail = computed(() => user.value?.email || '');
    const userInitials = computed(() => {
        if (!user.value?.name) return '';
        return user.value.name
            .split(' ')
            .map(word => word.charAt(0).toUpperCase())
            .join('')
            .substring(0, 2);
    });

    // Actions
    const setUser = (userData) => {
        user.value = userData;
        error.value = null;
    };

    const login = async (credentials) => {
        loading.value = true;
        error.value = null;
        
        try {
            const response = await fetch('/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify(credentials)
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Login failed');
            }
            
            const data = await response.json();
            user.value = data.user;
            
            // Redirect to dashboard or intended page
            router.visit(data.redirect || '/dashboard');
            
        } catch (err) {
            error.value = err.message;
            console.error('Login error:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const register = async (userData) => {
        loading.value = true;
        error.value = null;
        
        try {
            const response = await fetch('/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify(userData)
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Registration failed');
            }
            
            const data = await response.json();
            user.value = data.user;
            
            // Redirect to dashboard
            router.visit('/dashboard');
            
        } catch (err) {
            error.value = err.message;
            console.error('Registration error:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const logout = async () => {
        loading.value = true;
        error.value = null;
        
        try {
            const response = await fetch('/logout', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });
            
            if (!response.ok) {
                throw new Error('Logout failed');
            }
            
            user.value = null;
            
            // Redirect to login page
            router.visit('/login');
            
        } catch (err) {
            error.value = err.message;
            console.error('Logout error:', err);
            // Even if logout fails on server, clear local user data
            user.value = null;
            router.visit('/login');
        } finally {
            loading.value = false;
        }
    };

    const updateProfile = async (profileData) => {
        loading.value = true;
        error.value = null;
        
        try {
            const response = await fetch('/profile', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify(profileData)
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Profile update failed');
            }
            
            const updatedUser = await response.json();
            user.value = { ...user.value, ...updatedUser };
            
            return updatedUser;
        } catch (err) {
            error.value = err.message;
            console.error('Profile update error:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const changePassword = async (passwordData) => {
        loading.value = true;
        error.value = null;
        
        try {
            const response = await fetch('/password', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify(passwordData)
            });
            
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Password change failed');
            }
            
        } catch (err) {
            error.value = err.message;
            console.error('Password change error:', err);
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const clearError = () => {
        error.value = null;
    };

    const fetchUser = async () => {
        try {
            const response = await fetch('/api/user');
            if (response.ok) {
                const userData = await response.json();
                user.value = userData;
            }
        } catch (err) {
            console.error('Error fetching user:', err);
        }
    };

    return {
        // State
        user,
        loading,
        error,
        
        // Computed
        isAuthenticated,
        userName,
        userEmail,
        userInitials,
        
        // Actions
        setUser,
        login,
        register,
        logout,
        updateProfile,
        changePassword,
        clearError,
        fetchUser
    };
});