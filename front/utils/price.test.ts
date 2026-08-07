import { describe, expect, it } from 'vitest'
import { formatPrice } from './price'

/**
 * Logique pure : pas d'environnement Nuxt, donc pas de `.nuxt.test.ts`.
 *
 * Intl insère une espace fine insécable (U+202F) avant le symbole monétaire, et
 * pas une espace ordinaire. Comparer à une chaîne écrite au clavier donne un
 * échec où l'attendu et l'obtenu s'affichent à l'identique — on normalise donc
 * les espaces plutôt que de figer un caractère invisible dans le test.
 */
const normalize = (value: string): string => value.replace(/\s/gu, ' ')

describe('formatPrice', () => {
  it('convertit les centimes en unité d\'affichage', () => {
    expect(normalize(formatPrice(1250))).toBe('12,50 €')
  })

  it('respecte la devise transmise', () => {
    expect(normalize(formatPrice(1000, 'USD', 'en-US'))).toBe('$10.00')
  })

  it('traite un montant absent comme zéro plutôt que de rendre NaN', () => {
    expect(normalize(formatPrice(null))).toBe('0,00 €')
    expect(normalize(formatPrice(undefined))).toBe('0,00 €')
  })

  it('retombe sur l\'euro quand la devise est vide', () => {
    expect(normalize(formatPrice(500, ''))).toBe('5,00 €')
  })
})
