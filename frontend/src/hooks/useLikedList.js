import { useInfiniteQuery } from '@tanstack/react-query';
import api from '../services/api';

/**
 * Custom hook for fetching the list of users the current user has liked
 * Uses React Query's useInfiniteQuery for pagination support
 */
export const useLikedList = () => {
  return useInfiniteQuery({
    queryKey: ['likedUsers'],

    // Initial query function - fetches first page of liked users
    queryFn: async ({ pageParam = 1 }) => {
      const response = await api.get(`/users/liked?page=${pageParam}`);
      return response.data;
    },

    // Function to determine the next page parameter
    getNextPageParam: (lastPage, pages) => {
      // Laravel pagination returns next_page_url when there are more pages
      if (lastPage.next_page_url) {
        // Extract page number from next_page_url
        const url = new URL(lastPage.next_page_url);
        const nextPage = url.searchParams.get('page');
        return nextPage ? parseInt(nextPage, 10) : null;
      }
      return null; // No more pages
    },

    // Configuration options following React Query best practices
    staleTime: 5 * 60 * 1000, // 5 minutes - data is fresh for 5 minutes
    gcTime: 10 * 60 * 1000, // 10 minutes - keep in cache for 10 minutes
    retry: 3, // Retry failed requests 3 times
    retryDelay: (attemptIndex) => Math.min(1000 * 2 ** attemptIndex, 30000), // Exponential backoff

    // Error handling
    throwOnError: false, // Don't throw errors, handle them in the component

    // Refetch configuration
    refetchOnWindowFocus: false, // Don't refetch when window regains focus
    refetchOnMount: true, // Always refetch when component mounts
    refetchOnReconnect: true, // Refetch when network reconnects
  });
};

/**
 * Utility function to flatten all liked users pages into a single array
 * Useful for components that need all liked users at once
 */
export const useAllLikedUsers = () => {
  const query = useLikedList();

  return {
    ...query,
    data: query.data?.pages?.flatMap(page => page.data) || [],
    total: query.data?.pages?.[0]?.total || 0,
  };
};

/**
 * Utility function to get basic liked users info for quick access
 * Returns essential data without full pagination details
 */
export const useLikedUsersCount = () => {
  const { data, ...query } = useAllLikedUsers();

  return {
    ...query,
    count: data?.length || 0,
    total: query.total,
  };
};

export default useLikedList;