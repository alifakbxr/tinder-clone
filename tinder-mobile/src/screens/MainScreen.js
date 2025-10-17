import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  Dimensions,
  TouchableOpacity,
  Alert,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { useRecoilState, useRecoilValue } from 'recoil';
import { authState, authStateUtils, authPersistenceUtils, currentUserSelector } from '../state/authState';
import { useRecommendations } from '../hooks/useRecommendations';
import { useSwipeAction } from '../hooks/useSwipeAction';
import OpponentCard from '../components/organisms/OpponentCard';

const { width, height } = Dimensions.get('window');

const MainScreen = () => {
  const navigation = useNavigation();
  const [auth, setAuth] = useRecoilState(authState);
  const currentUser = useRecoilValue(currentUserSelector);
  const { recommendations, loading, error, fetchRecommendations } = useRecommendations();
  const { performSwipe } = useSwipeAction();

  const [currentIndex, setCurrentIndex] = useState(0);

  useEffect(() => {
    if (auth.isAuthenticated) {
      fetchRecommendations();
    }
  }, [auth.isAuthenticated]);

  const handleLogout = async () => {
    Alert.alert(
      'Logout',
      'Are you sure you want to logout?',
      [
        {
          text: 'Cancel',
          style: 'cancel',
        },
        {
          text: 'Logout',
          style: 'destructive',
          onPress: async () => {
            try {
              // Clear authentication state
              await authPersistenceUtils.clearAuthState();
              setAuth(authStateUtils.logout());
              navigation.reset({
                index: 0,
                routes: [{ name: 'Login' }],
              });
            } catch (error) {
              console.error('Logout error:', error);
              Alert.alert('Error', 'Failed to logout. Please try again.');
            }
          },
        },
      ]
    );
  };

  const handleSwipe = async (direction) => {
    if (!recommendations[currentIndex]) return;

    try {
      await performSwipe(recommendations[currentIndex].id, direction === 'right');
      setCurrentIndex(prev => prev + 1);
    } catch (error) {
      console.error('Swipe error:', error);
      Alert.alert('Error', 'Failed to process swipe. Please try again.');
    }
  };

  const handleLike = () => handleSwipe('right');
  const handlePass = () => handleSwipe('left');

  if (loading && recommendations.length === 0) {
    return (
      <View style={styles.centerContainer}>
        <Text style={styles.loadingText}>Finding matches...</Text>
      </View>
    );
  }

  if (error) {
    return (
      <View style={styles.centerContainer}>
        <Text style={styles.errorText}>{error}</Text>
        <TouchableOpacity style={styles.retryButton} onPress={fetchRecommendations}>
          <Text style={styles.retryButtonText}>Try Again</Text>
        </TouchableOpacity>
      </View>
    );
  }

  if (recommendations.length === 0 || currentIndex >= recommendations.length) {
    return (
      <View style={styles.centerContainer}>
        <Text style={styles.noMoreText}>No more profiles to show</Text>
        <TouchableOpacity style={styles.refreshButton} onPress={() => {
          setCurrentIndex(0);
          fetchRecommendations();
        }}>
          <Text style={styles.refreshButtonText}>Refresh</Text>
        </TouchableOpacity>
      </View>
    );
  }

  const currentProfile = recommendations[currentIndex];

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <TouchableOpacity onPress={handleLogout} style={styles.logoutButton}>
          <Text style={styles.logoutButtonText}>Logout</Text>
        </TouchableOpacity>
        <Text style={styles.headerTitle}>Tinder Clone</Text>
        <View style={styles.headerSpacer} />
      </View>

      {/* Profile Card */}
      <View style={styles.profileContainer}>
        <OpponentCard
          user={currentProfile}
          style={styles.profileCard}
        />
      </View>

      {/* Action Buttons */}
      <View style={styles.actionContainer}>
        <TouchableOpacity
          style={[styles.actionButton, styles.passButton]}
          onPress={handlePass}
        >
          <Text style={[styles.actionButtonText, styles.passButtonText]}>✕</Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={[styles.actionButton, styles.likeButton]}
          onPress={handleLike}
        >
          <Text style={[styles.actionButtonText, styles.likeButtonText]}>♥</Text>
        </TouchableOpacity>
      </View>

      {/* User Info */}
      {currentUser && (
        <View style={styles.userInfo}>
          <Text style={styles.userInfoText}>
            Welcome back, {currentUser.name || currentUser.email}!
          </Text>
        </View>
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#FFFFFF',
  },
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#FFFFFF',
    paddingHorizontal: 20,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingTop: 50,
    paddingBottom: 20,
    backgroundColor: '#FFFFFF',
    borderBottomWidth: 1,
    borderBottomColor: '#E0E0E0',
  },
  logoutButton: {
    paddingVertical: 8,
    paddingHorizontal: 12,
  },
  logoutButtonText: {
    color: '#FF6B6B',
    fontSize: 16,
    fontWeight: '500',
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#333333',
  },
  headerSpacer: {
    width: 60,
  },
  profileContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  actionContainer: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    alignItems: 'center',
    paddingHorizontal: 40,
    paddingBottom: 40,
  },
  actionButton: {
    width: 60,
    height: 60,
    borderRadius: 30,
    justifyContent: 'center',
    alignItems: 'center',
  },
  passButton: {
    backgroundColor: '#FF6B6B',
  },
  likeButton: {
    backgroundColor: '#4CAF50',
  },
  actionButtonText: {
    fontSize: 24,
    fontWeight: 'bold',
    color: '#FFFFFF',
  },
  passButtonText: {
    color: '#FFFFFF',
  },
  likeButtonText: {
    color: '#FFFFFF',
  },
  userInfo: {
    position: 'absolute',
    top: 120,
    right: 20,
    backgroundColor: 'rgba(255, 255, 255, 0.9)',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 15,
  },
  userInfoText: {
    fontSize: 12,
    color: '#333333',
  },
  loadingText: {
    fontSize: 18,
    color: '#666666',
    marginBottom: 20,
  },
  errorText: {
    fontSize: 16,
    color: '#FF6B6B',
    textAlign: 'center',
    marginBottom: 20,
  },
  retryButton: {
    backgroundColor: '#FF6B6B',
    paddingHorizontal: 20,
    paddingVertical: 10,
    borderRadius: 8,
  },
  retryButtonText: {
    color: '#FFFFFF',
    fontSize: 16,
    fontWeight: '500',
  },
  noMoreText: {
    fontSize: 18,
    color: '#666666',
    marginBottom: 20,
  },
  refreshButton: {
    backgroundColor: '#FF6B6B',
    paddingHorizontal: 20,
    paddingVertical: 10,
    borderRadius: 8,
  },
  refreshButtonText: {
    color: '#FFFFFF',
    fontSize: 16,
    fontWeight: '500',
  },
});

export default MainScreen;