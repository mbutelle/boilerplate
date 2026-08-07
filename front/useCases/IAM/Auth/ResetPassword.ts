import { useApiUserspasswordResetRequestsTokenapplyPost } from '@bp/api-client/api/password-reset/password-reset'

export default () => {
  const mutation = useApiUserspasswordResetRequestsTokenapplyPost()

  return {
    ...mutation,
    resetPassword: (token: string, password: string) =>
      mutation.mutateAsync({ token, data: { password } }),
  }
}
