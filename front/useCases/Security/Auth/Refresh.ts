import {
  gesdinetJwtRefreshToken,
} from '~/services/client'

export const refreshToken = async (): Promise<boolean> => {
  const token = useCookie('token')
  const refreshToken = useCookie('refreshToken')

  if (!refreshToken.value) {
    return false
  }

  const data = await gesdinetJwtRefreshToken({ refresh_token: refreshToken.value })

  token.value = data.token
  refreshToken.value = data.refresh_token

  return true
}
