import { jwtDecode, type JwtPayload } from 'jwt-decode'

export interface LoggerUser extends JwtPayload {
  roles: string[]
  email: string
  iat: number
  exp: number
  baseToken: string
}

const refresh = async (): Promise<LoggerUser | null> => {
  const token = useCookie('token')

  if (undefined === token.value || null === token.value) {
    return null
  }

  return jwtDecode(token.value) as LoggerUser
}

export const logOutUser = (): void => {
  const token = useCookie('token')
  const refreshToken = useCookie('refreshToken')

  token.value = null
  refreshToken.value = null

  navigateTo('/auth/login')
}

export const useLoggedUser = async (): Promise<LoggerUser | null> => {
  const token = useCookie('token')
  const refreshToken = useCookie('refreshToken')

  if (undefined === token.value || null === token.value) {
    return null
  }

  let jwt = jwtDecode(token.value) as LoggerUser

  if (Date.now() >= 1000 * jwt.exp) {
    const refreshedToken = await refresh()

    if (null === refreshedToken) {
      token.value = null
      refreshToken.value = null

      navigateTo('/auth/login')
      return null
    }

    jwt = refreshedToken
  }

  jwt.baseToken = token.value
  jwt.roles = Object.values(jwt.roles)

  if (!jwt.roles.includes('ROLE_ADMIN')) {
    token.value = null
    refreshToken.value = null

    navigateTo('/auth/login')
  }

  return jwt
}
