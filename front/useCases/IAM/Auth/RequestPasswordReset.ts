import { useApiUserspasswordResetRequestssendPost } from '@bp/api-client/api/password-reset/password-reset'

export default () => {
  const mutation = useApiUserspasswordResetRequestssendPost()

  return {
    ...mutation,
    requestReset: (email: string) => mutation.mutateAsync({ data: { email } }),
  }
}
