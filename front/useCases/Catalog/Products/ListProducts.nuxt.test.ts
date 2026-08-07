// @vitest-environment nuxt
import { describe, expect, it, vi } from 'vitest'
import { ref } from 'vue'

// Le SDK est un import explicite, donc vi.mock — mockNuxtImport ne s'applique
// qu'aux auto-imports de Nuxt et serait ici silencieusement ignoré.
// Le module est mocké en entier : Vitest 4 interdit les mocks partiels.
const useListProducts = vi.fn()

vi.mock('@bp/api-client/api/product/product', () => ({
  useListProducts: (...args: unknown[]) => useListProducts(...args),
  getListProductsQueryKey: () => ['products'],
  useCreateProduct: vi.fn(),
  usePublishProduct: vi.fn(),
  useDeleteProduct: vi.fn(),
  useUpdateProduct: vi.fn(),
  useGetProduct: vi.fn(),
}))

const { default: ListProducts } = await import('./ListProducts')

describe('ListProducts', () => {
  it('expose la page et le total renvoyés par l\'API', () => {
    useListProducts.mockReturnValue({
      data: ref({ member: [{ id: '1', name: 'Café' }], totalItems: 42 }),
    })

    const { products, totalItems } = ListProducts(ref({}))

    expect(products.value).toHaveLength(1)
    expect(totalItems.value).toBe(42)
  })

  it('rend une liste vide tant que la requête n\'a pas répondu', () => {
    useListProducts.mockReturnValue({ data: ref(undefined) })

    const { products, totalItems } = ListProducts(ref({}))

    // Sans ce repli, l'écran itérerait sur undefined au premier rendu.
    expect(products.value).toEqual([])
    expect(totalItems.value).toBe(0)
  })

  it('transmet les paramètres au hook généré, sans les recopier', () => {
    useListProducts.mockReturnValue({ data: ref(undefined) })

    const params = ref({ page: 2, status: 'draft' as const })
    ListProducts(params)

    expect(useListProducts).toHaveBeenCalledWith(params, expect.anything())
  })

  it('valide la réponse contre le schéma du contrat', () => {
    useListProducts.mockReturnValue({ data: ref(undefined) })

    ListProducts(ref({}))

    // La validation passe par `select` : c'est ce qui la limite aux réponses
    // fraîches au lieu de la rejouer à chaque lecture du cache.
    const [, options] = useListProducts.mock.calls.at(-1) as [unknown, { query: { select: unknown } }]
    expect(options.query.select).toBeTypeOf('function')
  })
})
