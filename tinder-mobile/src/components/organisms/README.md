# OpponentCard Component

## Overview
The `OpponentCard` is an organism component that displays user profiles in an attractive card format for the swipe interface. It follows Atomic Design principles and provides a polished, interactive card layout for displaying potential matches.

## Features

### ✨ Core Functionality
- **User Profile Display**: Shows user pictures, name, and age in an elegant card format
- **Picture Handling**: Supports multiple pictures with visual indicators
- **Gradient Overlays**: Includes gradient backgrounds for improved text readability
- **Responsive Design**: Adapts to different screen sizes with proper proportions
- **Touch Interaction**: Supports press interactions for user engagement

### 🎨 Visual Design
- **Modern Card Layout**: Rounded corners with shadow effects for depth
- **Typography**: Large, bold text for names with proper text shadows
- **Color Scheme**: Uses a gradient overlay from transparent to dark for text contrast
- **Picture Indicators**: Shows dots for multiple pictures with active state highlighting

## Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| `user` | `Object` | Yes | User object containing profile data |
| `onPress` | `Function` | No | Callback function when card is pressed |
| `style` | `Object` | No | Additional custom styles for the card |

### User Object Structure
```javascript
{
  id: number,
  name: string,
  age: number,
  pictures: [
    {
      id: number,
      url: string,
      is_primary: boolean
    }
  ]
}
```

## Usage

### Basic Usage
```jsx
import OpponentCard from '../components/organisms/OpponentCard';

const SwipeScreen = () => {
  const currentUser = {
    id: 1,
    name: "John Doe",
    age: 25,
    pictures: [
      { id: 1, url: "https://example.com/image1.jpg", is_primary: true },
      { id: 2, url: "https://example.com/image2.jpg", is_primary: false }
    ]
  };

  return (
    <View style={styles.container}>
      <OpponentCard user={currentUser} />
    </View>
  );
};
```

### With Press Handler
```jsx
<OpponentCard
  user={currentUser}
  onPress={(user) => {
    console.log('Card pressed for user:', user.name);
    // Navigate to user profile or show details
  }}
/>
```

### With Custom Styling
```jsx
<OpponentCard
  user={currentUser}
  style={{
    marginTop: 20,
    shadowColor: '#000',
    shadowOpacity: 0.5,
  }}
/>
```

## Picture Handling

### Single Picture
When a user has only one picture, it displays prominently without indicator dots.

### Multiple Pictures
When a user has multiple pictures:
- The first picture (or primary picture) is displayed
- Indicator dots show the total number of pictures
- The first dot is highlighted as active
- **Note**: Currently shows the first picture only. Future enhancement could include swipeable picture gallery.

### No Pictures
When no pictures are available, displays a placeholder with "No Image Available" text.

## Styling

The component uses the following design principles:

- **Card Dimensions**: 90% of screen width, 75% of screen height
- **Border Radius**: 20px for modern, rounded appearance
- **Shadow Effects**: Elevated shadow for depth perception
- **Typography**: 28px bold for names, 22px semibold for ages
- **Text Colors**: White text with shadow for readability over images
- **Gradient**: 4-stop gradient from transparent to dark overlay

## Dependencies

- `expo-linear-gradient`: For gradient overlay effects
- React Native core components: View, Text, Image, TouchableOpacity

## Integration Example

The component is currently integrated in `MainScreen.js` for the main swiping interface:

```jsx
// In MainScreen.js
<OpponentCard
  user={currentProfile}
  style={styles.profileCard}
/>
```

## Future Enhancements

- **Swipeable Gallery**: Add horizontal swipe through multiple pictures
- **Loading States**: Add skeleton loading for better UX
- **Animation**: Add entrance animations and swipe gestures
- **Accessibility**: Improve screen reader support and touch targets

## Best Practices

1. **Prop Validation**: Always pass a valid user object with required fields
2. **Error Handling**: Component gracefully handles missing pictures or data
3. **Performance**: Images use `resizeMode="cover"` for optimal performance
4. **Responsive**: Card dimensions adapt to different screen sizes
5. **Accessibility**: Consider adding accessibility labels for screen readers