import React from 'react';
import { TouchableOpacity, Text } from 'react-native';
import { createStackNavigator } from '@react-navigation/stack';
import { NavigationContainer } from '@react-navigation/native';

// Import screens
import SplashScreen from '../screens/SplashScreen';
import MainScreen from '../screens/MainScreen';
import LikedListScreen from '../screens/LikedListScreen';

const Stack = createStackNavigator();

const AppNavigator = () => {
  return (
    <NavigationContainer>
      <Stack.Navigator
        initialRouteName="SplashScreen"
        screenOptions={{
          headerStyle: {
            backgroundColor: '#FF6B6B',
          },
          headerTintColor: '#FFFFFF',
          headerTitleStyle: {
            fontWeight: 'bold',
          },
          gestureEnabled: true,
          gestureDirection: 'horizontal',
        }}
      >
        <Stack.Screen
          name="SplashScreen"
          component={SplashScreen}
          options={{
            headerShown: false,
            title: 'Welcome',
          }}
        />
        <Stack.Screen
          name="MainScreen"
          component={MainScreen}
          options={({ navigation }) => ({
            title: 'Tinder Clone',
            headerLeft: null, // Remove back button on main screen
            headerRight: () => (
              <TouchableOpacity
                style={{ marginRight: 15 }}
                onPress={() => navigation.navigate('LikedListScreen')}
              >
                <Text style={{ color: '#FFFFFF', fontSize: 16, fontWeight: 'bold' }}>
                  Liked
                </Text>
              </TouchableOpacity>
            ),
          })}
        />
        <Stack.Screen
          name="LikedListScreen"
          component={LikedListScreen}
          options={{
            title: 'Liked Users',
            headerBackTitleVisible: false,
          }}
        />
      </Stack.Navigator>
    </NavigationContainer>
  );
};

export default AppNavigator;