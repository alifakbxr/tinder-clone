// User types based on Laravel UserResource
export const User = {
  id: 'number',
  name: 'string',
  age: 'number',
  latitude: 'number | null',
  longitude: 'number | null',
  pictures: 'array',
  created_at: 'string',
  updated_at: 'string',
};

// Laravel pagination response structure
export const PaginatedResponse = {
  current_page: 'number',
  data: `array<${User}>`,
  first_page_url: 'string | null',
  from: 'number | null',
  last_page: 'number',
  last_page_url: 'string | null',
  links: 'array',
  next_page_url: 'string | null',
  path: 'string',
  per_page: 'number',
  prev_page_url: 'string | null',
  to: 'number | null',
  total: 'number',
};

// API Response types
export const ApiResponse = 'PaginatedResponse';

// User picture type (if needed for detailed typing)
export const UserPicture = {
  id: 'number',
  user_id: 'number',
  image_url: 'string',
  is_primary: 'boolean',
  created_at: 'string',
  updated_at: 'string',
};