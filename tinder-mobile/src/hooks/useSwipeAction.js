import { useMutation, useQueryClient } from '@tanstack/react-query';
import api from '../services/api';

/**
 * Custom hook for handling swipe actions (like/nope) with optimistic updates
 * Uses React Query mutations for state management and cache synchronization
 */
export const useSwipeAction = () => {
  const queryClient = useQueryClient();

  return useMutation({
    // Mutation function that sends the swipe action to the backend
    mutationFn: async ({ swipedUserId, action }) => {
      const response = await api.post('/swipes', {
        swiped_id: swipedUserId,
        action: action, // 'like' or 'nope'
      });
      return response.data;
    },

    // Optimistic updates - immediately remove the swiped user from cache
    onMutate: async ({ swipedUserId }) => {
      // Cancel any outgoing refetches (so they don't overwrite our optimistic update)
      await queryClient.cancelQueries({ queryKey: ['recommendations'] });

      // Snapshot the previous value for rollback
      const previousRecommendations = queryClient.getQueryData(['recommendations']);

      // Optimistically update the cache by removing the swiped user
      queryClient.setQueryData(['recommendations'], (old) => {
        if (!old) return old;

        return {
          ...old,
          pages: old.pages.map(page => ({
            ...page,
            data: page.data.filter(user => user.id !== swipedUserId),
            total: page.total - 1,
          })),
        };
      });

      // Return a context object with the snapshotted value for rollback
      return { previousRecommendations };
    },

    // If the mutation fails, use the context returned from onMutate to roll back
    onError: (err, variables, context) => {
      if (context?.previousRecommendations) {
        queryClient.setQueryData(['recommendations'], context.previousRecommendations);
      }
      console.error('Swipe action failed:', err);
    },

    // Always refetch after error or success to ensure cache consistency
    onSettled: () => {
      queryClient.invalidateQueries({ queryKey: ['recommendations'] });
    },

    // Additional configuration
    retry: 2, // Retry failed mutations 2 times
    retryDelay: (attemptIndex) => Math.min(1000 * 2 ** attemptIndex, 30000), // Exponential backoff
  });
};

/**
 * Convenience hook for like actions
 */
export const useLikeAction = () => {
  const mutation = useSwipeAction();

  return {
    ...mutation,
    mutate: (swipedUserId) => mutation.mutate({ swipedUserId, action: 'like' }),
    mutateAsync: (swipedUserId) => mutation.mutateAsync({ swipedUserId, action: 'like' }),
  };
};

/**
 * Convenience hook for nope actions
 */
export const useNopeAction = () => {
  const mutation = useSwipeAction();

  return {
    ...mutation,
    mutate: (swipedUserId) => mutation.mutate({ swipedUserId, action: 'nope' }),
    mutateAsync: (swipedUserId) => mutation.mutateAsync({ swipedUserId, action: 'nope' }),
  };
};

export default useSwipeAction;