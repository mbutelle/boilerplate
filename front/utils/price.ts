/**
 * L'API transporte les montants en centimes ; la conversion en unité
 * d'affichage vit ici, hors des composants, pour être testable sans monter Nuxt.
 */
export const formatPrice = (
  amountInCents?: number | null,
  currency?: string | null,
  locale = 'fr-FR',
): string =>
  new Intl.NumberFormat(locale, {
    style: 'currency',
    currency: currency || 'EUR',
  }).format((amountInCents ?? 0) / 100)
