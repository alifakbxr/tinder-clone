import React, { useEffect } from 'react';
import { View, Text, StyleSheet, ActivityIndicator, Alert } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { useRecoilState, useSetRecoilState } from 'recoil';
import { authState, authPersistenceUtils, isTokenValid, authStateUtils } from '../state/authState';

const SplashScreen = () => {
  const navigation = useNavigation();
  const [auth, setAuth] = useRecoilState(authState);
  const setAuthState = useSetRecoilState(authState);

  useEffect(() => {
    checkAuthenticationStatus();
  }, []);

  const checkAuthenticationStatus = async () => {
    try {
      setAuthState(authStateUtils.setLoading(true));

      // Load stored authentication state
      const storedAuth = await authPersistenceUtils.loadAuthState();

      if (storedAuth && storedAuth.token && storedAuth.user) {
        // Validate the stored token
        if (isTokenValid(storedAuth.token)) {
          // Token is valid, restore authentication state
          setAuthState(authStateUtils.login(storedAuth.token, storedAuth.user));

          // Navigate to main screen
          setTimeout(() => {
            navigation.replace('MainScreen');
          }, 1500); // Show splash for 1.5 seconds
        } else {
          // Token is invalid, clear stored data and navigate to login
          await authPersistenceUtils.clearAuthState();
          setAuthState(authStateUtils.logout());

          setTimeout(() => {
            navigation.replace('Login');
          }, 1500);
        }
      } else {
        // No stored authentication, navigate to login
        setAuthState(authStateUtils.logout());

        setTimeout(() => {
          navigation.replace('Login');
        }, 1500);
      }
    } catch (error) {
      console.error('Error checking authentication status:', error);

      // Show error and navigate to login
      Alert.alert(
        'Authentication Error',
        'There was an error checking your authentication status. Please try again.',
        [
          {
            text: 'OK',
            onPress: () => {
              setAuthState(authStateUtils.setError('Authentication check failed'));
              navigation.replace('Login');
            }
          }
        ]
      );
    }
  };

  return (
    <View style={styles.container}>
      <View style={styles.logoContainer}>
        <Text style={styles.logoText}>Tinder Clone</Text>
        <Text style={styles.tagline}>Find your match</Text>
      </View>

      {auth.isLoading && (
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color="#FF6B6B" />
          <Text style={styles.loadingText}>Checking authentication...</Text>
        </View>
      )}

      {auth.error && (
        <View style={styles.errorContainer}>
          <Text style={styles.errorText}>{auth.error}</Text>
        </View>
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#FF6B6B',
    justifyContent: 'center',
    alignItems: 'center',
  },
  logoContainer: {
    alignItems: 'center',
    marginBottom: 50,
  },
  logoText: {
    fontSize: 32,
    fontWeight: 'bold',
    color: '#FFFFFF',
    marginBottom: 10,
  },
  tagline: {
    fontSize: 16,
    color: '#FFFFFF',
    opacity: 0.8,
  },
  loadingContainer: {
    alignItems: 'center',
  },
  loadingText: {
    marginTop: 20,
    fontSize: 16,
    color: '#FFFFFF',
    opacity: 0.8,
  },
  errorContainer: {
    position: 'absolute',
    bottom: 50,
    left: 20,
    right: 20,
    backgroundColor: 'rgba(255, 255, 255, 0.9)',
    padding: 15,
    borderRadius: 10,
  },
  errorText: {
    color: '#FF6B6B',
    fontSize: 14,
    textAlign: 'center',
  },
});

export default SplashScreen;