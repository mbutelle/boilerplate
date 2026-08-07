import { describe, expect, it, vi } from 'vitest'
import { z } from 'zod'
import { validateResponse } from './validateResponse'

const schema = z.object({ id: z.string(), total: z.number() })

describe('validateResponse', () => {
  it('renvoie la donnée quand elle respecte le contrat', () => {
    const data = { id: 'a', total: 3 }

    expect(validateResponse(schema, data, 'GET /exemple', true)).toEqual(data)
  })

  it('échoue bruyamment en mode strict sur une réponse non conforme', () => {
    expect(() => validateResponse(schema, { id: 'a' }, 'GET /exemple', true)).toThrow(/contrat/)
  })

  it('hors mode strict, trace et laisse passer plutôt que de casser l\'écran', () => {
    const error = vi.spyOn(console, 'error').mockImplementation(() => {})
    const malformed = { id: 'a' }

    expect(validateResponse(schema, malformed, 'GET /exemple', false)).toBe(malformed)
    expect(error).toHaveBeenCalledOnce()

    error.mockRestore()
  })
})
