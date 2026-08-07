import { gesdinetJwtRefreshToken } from '@bp/api-client/api/refresh-token/refresh-token'

export const refreshToken = async (): Promise<boolean> => {
  const token = useCookie('token')
  const refresh = useCookie('refreshToken')

  if (!refresh.value) {
    return false
  }

  const data = await gesdinetJwtRefreshToken({ refresh_token: refresh.value })

  token.value = data.token
  refresh.value = data.refresh_token

  return true
}
