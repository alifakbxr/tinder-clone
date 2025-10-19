import { atom, selector } from 'recoil';

// Auth state atom to store authentication data
export const authState = atom({
  key: 'authState',
  default: {
    isAuthenticated: false,
    token: null,
    user: null,
    isLoading: false,
    error: null,
  },
});

// Selector to get authentication status
export const isAuthenticatedSelector = selector({
  key: 'isAuthenticatedSelector',
  get: ({ get }) => {
    const auth = get(authState);
    return auth.isAuthenticated && auth.token !== null;
  },
});

// Selector to get current user data
export const currentUserSelector = selector({
  key: 'currentUserSelector',
  get: ({ get }) => {
    const auth = get(authState);
    return auth.user;
  },
});

// Selector to get authentication token
export const authTokenSelector = selector({
  key: 'authTokenSelector',
  get: ({ get }) => {
    const auth = get(authState);
    return auth.token;
  },
});

// Selector to check if auth is loading
export const isAuthLoadingSelector = selector({
  key: 'isAuthLoadingSelector',
  get: ({ get }) => {
    const auth = get(authState);
    return auth.isLoading;
  },
});

// Selector to get auth error
export const authErrorSelector = selector({
  key: 'authErrorSelector',
  get: ({ get }) => {
    const auth = get(authState);
    return auth.error;
  },
});

// Utility functions for managing auth state
export const authStateUtils = {
  // Login function - updates state with user data and token
  login: (token, userData) => ({
    type: 'LOGIN',
    payload: { token, user: userData },
  }),

  // Logout function - clears auth state
  logout: () => ({
    type: 'LOGOUT',
  }),

  // Set loading state
  setLoading: (isLoading) => ({
    type: 'SET_LOADING',
    payload: { isLoading },
  }),

  // Set error state
  setError: (error) => ({
    type: 'SET_ERROR',
    payload: { error },
  }),

  // Clear error state
  clearError: () => ({
    type: 'CLEAR_ERROR',
  }),

  // Update user data
  updateUser: (userData) => ({
    type: 'UPDATE_USER',
    payload: { user: userData },
  }),
};

// Auth state reducer for handling state updates
export const authStateReducer = (currentState, action) => {
  switch (action.type) {
    case 'LOGIN':
      return {
        ...currentState,
        isAuthenticated: true,
        token: action.payload.token,
        user: action.payload.user,
        isLoading: false,
        error: null,
      };

    case 'LOGOUT':
      return {
        ...currentState,
        isAuthenticated: false,
        token: null,
        user: null,
        isLoading: false,
        error: null,
      };

    case 'SET_LOADING':
      return {
        ...currentState,
        isLoading: action.payload.isLoading,
      };

    case 'SET_ERROR':
      return {
        ...currentState,
        error: action.payload.error,
        isLoading: false,
      };

    case 'CLEAR_ERROR':
      return {
        ...currentState,
        error: null,
      };

    case 'UPDATE_USER':
      return {
        ...currentState,
        user: action.payload.user,
      };

    default:
      return currentState;
  }
};

// Async storage keys for persistence (if using React Native AsyncStorage)
export const AUTH_STORAGE_KEYS = {
  TOKEN: '@tinder_auth_token',
  USER_DATA: '@tinder_user_data',
};

// Token validation utility
export const isTokenValid = (token) => {
  if (!token) return false;

  try {
    // Basic token validation - you can enhance this based on your token structure
    const tokenParts = token.split('.');
    if (tokenParts.length !== 3) return false;

    // Decode payload to check expiration if it's a JWT
    const payload = JSON.parse(atob(tokenParts[1]));
    const currentTime = Date.now() / 1000;

    return payload.exp ? payload.exp > currentTime : true;
  } catch (error) {
    return false;
  }
};

// Auth state persistence utilities
export const authPersistenceUtils = {
  // Save auth state to storage
  saveAuthState: async (token, userData) => {
    try {
      // In React Native, you would use AsyncStorage here
      // For now, we'll use localStorage as fallback (for web compatibility)
      if (typeof localStorage !== 'undefined') {
        localStorage.setItem(AUTH_STORAGE_KEYS.TOKEN, token);
        localStorage.setItem(AUTH_STORAGE_KEYS.USER_DATA, JSON.stringify(userData));
      }
      return true;
    } catch (error) {
      console.error('Error saving auth state:', error);
      return false;
    }
  },

  // Load auth state from storage
  loadAuthState: async () => {
    try {
      if (typeof localStorage !== 'undefined') {
        const token = localStorage.getItem(AUTH_STORAGE_KEYS.TOKEN);
        const userDataString = localStorage.getItem(AUTH_STORAGE_KEYS.USER_DATA);

        if (token && userDataString) {
          const userData = JSON.parse(userDataString);

          // Validate token before returning
          if (isTokenValid(token)) {
            return { token, user: userData };
          } else {
            // Clear invalid token
            await authPersistenceUtils.clearAuthState();
            return null;
          }
        }
      }
      return null;
    } catch (error) {
      console.error('Error loading auth state:', error);
      return null;
    }
  },

  // Clear auth state from storage
  clearAuthState: async () => {
    try {
      if (typeof localStorage !== 'undefined') {
        localStorage.removeItem(AUTH_STORAGE_KEYS.TOKEN);
        localStorage.removeItem(AUTH_STORAGE_KEYS.USER_DATA);
      }
      return true;
    } catch (error) {
      console.error('Error clearing auth state:', error);
      return false;
    }
  },
};