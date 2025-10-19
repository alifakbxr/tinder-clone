import React from 'react';
import {
  View,
  Text,
  StyleSheet,
  Image,
  Dimensions,
  TouchableOpacity,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';

const { width, height } = Dimensions.get('window');

// OpponentCard organism component for displaying user profiles in swipe interface
const OpponentCard = ({
  user,
  onPress,
  style,
}) => {
  // Get the primary picture or first picture from user's pictures array
  const getDisplayPicture = () => {
    if (!user?.pictures || user.pictures.length === 0) {
      return null;
    }

    // Find primary picture first, otherwise use the first picture
    const primaryPicture = user.pictures.find(pic => pic.is_primary);
    return primaryPicture || user.pictures[0];
  };

  const displayPicture = getDisplayPicture();

  // Handle card press
  const handlePress = () => {
    if (onPress) {
      onPress(user);
    }
  };

  return (
    <TouchableOpacity
      style={[styles.card, style]}
      onPress={handlePress}
      activeOpacity={0.95}
    >
      {/* User Image */}
      <View style={styles.imageContainer}>
        {displayPicture ? (
          <Image
            source={{ uri: displayPicture.url }}
            style={styles.userImage}
            resizeMode="cover"
          />
        ) : (
          <View style={styles.noImageContainer}>
            <Text style={styles.noImageText}>No Image Available</Text>
          </View>
        )}
      </View>

      {/* Gradient Overlay for Text Readability */}
      <LinearGradient
        colors={[
          'transparent',
          'rgba(0, 0, 0, 0.1)',
          'rgba(0, 0, 0, 0.4)',
          'rgba(0, 0, 0, 0.7)',
        ]}
        style={styles.gradientOverlay}
      />

      {/* User Info Overlay */}
      <View style={styles.userInfoOverlay}>
        <View style={styles.userInfoContainer}>
          <Text style={styles.userName}>
            {user?.name || 'Unknown User'}
          </Text>
          <Text style={styles.userAge}>
            {user?.age ? `${user.age}` : '?'}
          </Text>
        </View>

        {/* Picture indicator dots for multiple pictures */}
        {user?.pictures && user.pictures.length > 1 && (
          <View style={styles.pictureIndicators}>
            {user.pictures.map((_, index) => (
              <View
                key={index}
                style={[
                  styles.pictureIndicator,
                  index === 0 && styles.activeIndicator, // Show first as active for now
                ]}
              />
            ))}
          </View>
        )}
      </View>
    </TouchableOpacity>
  );
};

const styles = StyleSheet.create({
  card: {
    width: width * 0.9,
    height: height * 0.75,
    borderRadius: 20,
    backgroundColor: '#FFFFFF',
    shadowColor: '#000',
    shadowOffset: {
      width: 0,
      height: 8,
    },
    shadowOpacity: 0.3,
    shadowRadius: 12,
    elevation: 10,
    position: 'relative',
    overflow: 'hidden',
  },
  imageContainer: {
    flex: 1,
    borderRadius: 20,
    overflow: 'hidden',
  },
  userImage: {
    width: '100%',
    height: '100%',
  },
  noImageContainer: {
    width: '100%',
    height: '100%',
    backgroundColor: '#F5F5F5',
    justifyContent: 'center',
    alignItems: 'center',
  },
  noImageText: {
    fontSize: 18,
    color: '#999999',
    fontWeight: '500',
  },
  gradientOverlay: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
  },
  userInfoOverlay: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    padding: 20,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-end',
  },
  userInfoContainer: {
    flex: 1,
  },
  userName: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#FFFFFF',
    marginBottom: 4,
    textShadowColor: 'rgba(0, 0, 0, 0.5)',
    textShadowOffset: { width: 0, height: 2 },
    textShadowRadius: 4,
  },
  userAge: {
    fontSize: 22,
    fontWeight: '600',
    color: '#FFFFFF',
    textShadowColor: 'rgba(0, 0, 0, 0.5)',
    textShadowOffset: { width: 0, height: 2 },
    textShadowRadius: 4,
  },
  pictureIndicators: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 4,
  },
  pictureIndicator: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: 'rgba(255, 255, 255, 0.5)',
    marginLeft: 4,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.3)',
  },
  activeIndicator: {
    backgroundColor: '#FFFFFF',
    borderColor: '#FFFFFF',
  },
});

export default OpponentCard;