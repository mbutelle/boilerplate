export default defineNuxtRouteMiddleware(async (to) => {
  const routeName = to.name

  if (!routeName) {
    return
  }

  if (-1 < ['password-expired', 'password-forgotten', 'reset-password'].indexOf(routeName.toString())) {
    return
  }

  if (-1 < ['login'].indexOf(routeName.toString())) {
    return
  }

  const loggedUser = await useLoggedUser()

  if (loggedUser) {
    if (typeof to.meta.role === 'string' && -1 === loggedUser.roles.indexOf(to.meta.role)) {
      return navigateTo('/')
    }

    return
  }

  return navigateTo('/auth/login')
})
