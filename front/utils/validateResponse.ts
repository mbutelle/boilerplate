import type { ZodType } from 'zod'

/**
 * Vérifie une réponse contre le schéma zod dérivé du contrat OpenAPI.
 *
 * En développement, une dérive du back échoue bruyamment, là où elle se produit
 * — plutôt que de se manifester trois écrans plus loin sous la forme d'un champ
 * indéfini. En production on ne casse pas l'écran pour autant : on trace et on
 * laisse passer la donnée, parce qu'un contrat qui bouge ne doit pas rendre
 * l'application inutilisable.
 *
 * `strict` est un paramètre plutôt qu'une lecture directe de `import.meta.dev`
 * dans le corps : Vite remplace cette valeur à la compilation, donc un test ne
 * peut pas la faire varier. La valeur par défaut garde l'appelant silencieux.
 */
export const validateResponse = <T>(
  schema: ZodType<T>,
  data: unknown,
  label: string,
  strict: boolean = import.meta.dev,
): T => {
  const result = schema.safeParse(data)

  if (result.success) {
    return result.data
  }

  if (strict) {
    throw new Error(
      `La réponse de ${label} ne respecte pas le contrat : ${result.error.message}`,
    )
  }

  console.error(`[api] réponse non conforme au contrat : ${label}`, result.error.issues)

  return data as T
}
