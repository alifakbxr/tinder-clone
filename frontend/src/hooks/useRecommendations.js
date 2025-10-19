import { useInfiniteQuery } from '@tanstack/react-query';
import api from '../services/api';

/**
 * Custom hook for fetching user recommendations with infinite scrolling
 * Uses React Query's useInfiniteQuery for pagination
 */
export const useRecommendations = () => {
  return useInfiniteQuery({
    queryKey: ['recommendations'],

    // Initial query function - fetches first page
    queryFn: async ({ pageParam = 1 }) => {
      const response = await api.get(`/users/recommendations?page=${pageParam}`);
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

    // Configuration options
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
 * Utility function to flatten all recommendation pages into a single array
 * Useful for components that need all recommendations at once
 */
export const useAllRecommendations = () => {
  const query = useRecommendations();

  return {
    ...query,
    data: query.data?.pages?.flatMap(page => page.data) || [],
    total: query.data?.pages?.[0]?.total || 0,
  };
};

/**
 * Utility function to get the next recommendation for swiping
 * Returns the first unseen recommendation from the flattened list
 */
export const useNextRecommendation = () => {
  const { data: recommendations, ...query } = useAllRecommendations();

  return {
    ...query,
    data: recommendations?.[0] || null,
    hasMore: recommendations && recommendations.length > 0,
  };
};

export default useRecommendations;