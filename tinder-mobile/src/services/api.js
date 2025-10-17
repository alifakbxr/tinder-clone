import axios from 'axios';
import { authTokenSelector } from '../state/authState';

// Create axios instance with base configuration
const api = axios.create({
  baseURL: 'http://localhost:8000/api', // Laravel backend URL
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Request interceptor to add auth token
api.interceptors.request.use(
  async (config) => {
    try {
      // Get auth token from Recoil state
      // Note: In a real app, you'd use useRecoilValue hook or similar
      // For now, we'll get it from localStorage as fallback
      const token = localStorage.getItem('@tinder_auth_token');

      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }
    } catch (error) {
      console.error('Error adding auth token to request:', error);
    }

    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor for error handling
api.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    if (error.response?.status === 401) {
      // Handle unauthorized access - clear auth state
      localStorage.removeItem('@tinder_auth_token');
      localStorage.removeItem('@tinder_user_data');
    }

    return Promise.reject(error);
  }
);

// Login method that sends form data instead of JSON
export const login = async (email, password) => {
  const formData = new URLSearchParams();
  formData.append('email', email);
  formData.append('password', password);

  return axios.post('/login', formData, {
    baseURL: 'http://localhost:8000/api',
    timeout: 10000,
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'Accept': 'application/json',
    },
  });
};

export default api;