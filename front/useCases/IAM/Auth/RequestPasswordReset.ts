import { useRequestPasswordReset } from '@bp/api-client/api/password-reset/password-reset'

export default () => {
  const mutation = useRequestPasswordReset()

  return {
    ...mutation,
    requestReset: (email: string) => mutation.mutateAsync({ data: { email } }),
  }
}
